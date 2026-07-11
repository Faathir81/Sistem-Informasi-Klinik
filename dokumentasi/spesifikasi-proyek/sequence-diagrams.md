# Sequence Diagram - Sistem Informasi Klinik Ar-Ridlo

Diagram menampilkan komponen aplikasi yang benar-benar terlibat pada tujuh proses utama.

## 1. Autentikasi Pengguna

```mermaid
sequenceDiagram
    actor Pengguna
    participant Web as Web (Router)
    participant Auth as AuthenticatedSessionController
    participant Login as LoginRequest
    participant User as User (Model)
    participant Panel as Filament Admin Panel
    participant PasienDash as DashboardController

    Pengguna->>Web: POST /login
    Web->>Auth: store(LoginRequest $request)
    Auth->>Login: authenticate()
    Login->>User: Auth::attempt($credentials)
    alt Auth::attempt() gagal
        Login-->>Pengguna: throw ValidationException
    else Auth::attempt() sukses
        Auth->>Web: $request->session()->regenerate()
        Auth->>User: Auth::user()->role
        alt role == 'admin'
            Auth->>Panel: redirect('/admin')
            Panel-->>Pengguna: HTTP 200 OK (View Dashboard Admin)
        else role == 'pasien'
            Auth->>PasienDash: redirect()->intended(route('pasien.dashboard'))
            PasienDash-->>Pengguna: index() : view('pasien.dashboard')
        end
    end
```

## 2. Pengajuan dan Aktivasi Pasien

```mermaid
sequenceDiagram
    actor Pasien
    participant Controller as PengajuanPasienController
    participant Pengajuan as PengajuanPasien
    participant Snap as MidtransSnapService
    participant Midtrans
    participant Webhook as WebhookController
    participant Transaksi
    participant PasienModel as Pasien

    Pasien->>Controller: store(StorePengajuanPasienRequest $request)
    Controller->>Pengajuan: StorePengajuanPasienRequest::validated() & PengajuanPasien::create()
    Controller->>Snap: createRegistrationTransaction($pengajuan)
    Snap->>Transaksi: Transaksi::updateOrCreate() (status = 'pending')
    Snap->>Midtrans: Http::post($snapEndpoint, $payload)
    Midtrans-->>Snap: JSON response: token & redirect_url
    Snap->>Transaksi: $transaksi->update(['snap_token', 'snap_url'])
    Controller-->>Pasien: redirect()->route('pasien.pembayaran.show', $transaksi)
    Pasien->>Midtrans: Snap payment redirect / SDK checkout
    Midtrans->>Webhook: __invoke(Request $request)
    Webhook->>Snap: isValidSignature($payload)
    alt [!isValidSignature()]
        Webhook-->>Midtrans: response()->json(['message' => 'Invalid signature'], 403)
    else [transaction_status == 'SETTLEMENT' or 'CAPTURE']
        Webhook->>Transaksi: markSettled($paymentType)
        Transaksi->>Pengajuan: approveFromPayment()
        Pengajuan->>PasienModel: Pasien::create() (triggers MedicalRecordNumberService::next())
        Pengajuan->>Pengajuan: $this->update(['status' => 'Disetujui', 'pasien_id' => $pasien->id])
        Webhook-->>Midtrans: response()->json(['message' => 'OK'])
    else [transaction_status == 'EXPIRE' or 'CANCEL' or 'DENY' or 'FAILURE']
        Webhook->>Transaksi: $transaksi->update(['status' => $status])
        Transaksi->>Pengajuan: markPaymentFailed()
        Webhook-->>Midtrans: response()->json(['message' => 'OK'])
    end
```

## 3. Booking Antrean

```mermaid
sequenceDiagram
    actor Pasien
    participant Controller as AntreanController
    participant Booking as AntreanBookingService
    participant Jadwal as JadwalDokter
    participant Libur as JadwalLibur
    participant Antrean
    participant PDF as DomPDF (Barryvdh\DomPDF)

    Pasien->>Controller: getJadwal(Request $request)
    Controller->>Booking: scheduleAvailability($dokterId, $tanggal)
    Booking->>Libur: holidayRecord($dokterId, $tanggalKunjungan)
    Booking->>Jadwal: availableSchedules($dokterId, $tanggal)
    Booking->>Antrean: Antrean::where('jadwal_dokter_id', $id)->count()
    Booking-->>Controller: array $availability
    Controller-->>Pasien: response()->json($availability)
    Pasien->>Controller: store(Request $request)
    Controller->>Booking: create(Pasien $pasien, array $data)
    Booking->>Jadwal: JadwalDokter::lockForUpdate()->firstOrFail(), ensureScheduleMatchesVisitDate(), ensureScheduleIsBookable()
    Booking->>Antrean: ensureAvailableQuota(), ensureNoActiveDuplicate()
    Booking->>Antrean: nextQueueNumber(), queueCode(), Antrean::create()
    Antrean-->>Controller: Antrean $antrean
    Controller-->>Pasien: redirect()->route('pasien.antrean.tiket', $antrean->kode_antrean)
    opt [unduh tiket]
        Pasien->>Controller: tiketPdf($kode)
        Controller->>PDF: Pdf::loadView('pasien.antrean.tiket-pdf')
        PDF-->>Pasien: download($filename)
    end
```

## 4. Pemeriksaan, Tindakan, dan Resep

```mermaid
sequenceDiagram
    actor Admin
    participant PemeriksaanResource as Filament resource form
    participant Pemeriksaan
    participant Tindakan as PemeriksaanTindakan
    participant ResepResource as Filament resource form
    participant Resep
    participant Detail as ResepDetail
    participant Stock as ResepDetailStockService
    participant Stok as StokObat
    participant Mutasi as StokObatMutasi

    Admin->>PemeriksaanResource: Form Submission (Save)
    PemeriksaanResource->>Pemeriksaan: Pemeriksaan::create() / update()
    opt [tindakan medis]
        PemeriksaanResource->>Tindakan: PemeriksaanTindakan::create()
    end
    Pemeriksaan->>Pemeriksaan: totalTindakan()
    PemeriksaanResource-->>Admin: redirect() / Notification::make()->success()

    opt [resep obat]
        Admin->>ResepResource: Form Submission (Save)
        ResepResource->>Resep: Resep::create() / update()
        ResepResource->>Detail: ResepDetail::create() / update()
        Detail->>Stock: prepareForSave(ResepDetail $detail) (saving event)
        Stock->>Detail: $detail->sub_total = $obat->harga_jual * $detail->jumlah
        Detail->>Stock: reserveForCreate() (creating event) / applyUpdating() (updating event)
        Stock->>Stok: ensureStock($obatId, $jumlah) (queries StokObat)
        alt [stok tidak mencukupi]
            Stock-->>Admin: throw ValidationException
        else [stok cukup]
            Stock->>Stok: takeStockFefo($detail, $obatId, $jumlah) (decrements StokObat)
            Stock->>Mutasi: StokObatMutasi::create([...])
            Stock->>Resep: recalculateTotal() (via applyCreated / applyUpdated)
            ResepResource-->>Admin: redirect() / Notification::make()->success()
        end
    end
```

## 5. Pembayaran Pemeriksaan

```mermaid
sequenceDiagram
    actor Pasien
    participant Controller as PembayaranController
    participant Pemeriksaan
    participant Snap as MidtransSnapService
    participant Midtrans
    participant Webhook as WebhookController
    participant Transaksi

    Pasien->>Controller: store(Request $request, Pemeriksaan $pemeriksaan)
    Controller->>Pemeriksaan: abort_unless($pemeriksaan->pasien->user_id === auth()->id(), 403)
    alt [bukan milik pasien]
        Controller-->>Pasien: abort(403)
    else [sudah lunas ($pemeriksaan->transaksi->status == 'settlement')]
        Controller-->>Pasien: redirect()->route('pasien.pembayaran.show', $transaksi)
    else [belum lunas]
        Controller->>Pemeriksaan: $totalPembayaran = $biayaKonsultasi + $totalObat + $totalTindakan
        alt [$totalPembayaran < 1000]
            Controller-->>Pasien: back()->withErrors(['biaya_konsultasi' => '...'])
        else [$totalPembayaran >= 1000]
            Controller->>Snap: createTransaction($pemeriksaan, $biayaKonsultasi)
            Snap->>Transaksi: Transaksi::updateOrCreate(...) (status = 'pending')
            Snap->>Midtrans: Http::post($snapEndpoint, $payload)
            Midtrans-->>Snap: JSON response: token & redirect_url
            Snap->>Transaksi: $transaksi->update(['snap_token', 'snap_url'])
            Controller-->>Pasien: redirect()->route('pasien.pembayaran.show', $transaksi)
            Pasien->>Midtrans: Snap payment redirect / SDK checkout
            Midtrans->>Webhook: __invoke(Request $request)
            Webhook->>Snap: isValidSignature($payload)
            alt [!isValidSignature()]
                Webhook-->>Midtrans: response()->json(['message' => 'Invalid signature'], 403)
            else [transaction_status == 'SETTLEMENT' or 'CAPTURE']
                Webhook->>Transaksi: markSettled($paymentType)
                Transaksi->>Pemeriksaan: $this->pemeriksaan->update(['status_bayar' => 'Lunas'])
                Webhook-->>Midtrans: response()->json(['message' => 'OK'])
            else [transaction_status == 'EXPIRE']
                Webhook->>Transaksi: markFailed($transaksi, TransaksiStatus::Expire)
                Webhook-->>Midtrans: response()->json(['message' => 'OK'])
            else [transaction_status == 'CANCEL' or 'DENY' or 'FAILURE']
                Webhook->>Transaksi: markFailed($transaksi, TransaksiStatus::Cancel)
                Webhook-->>Midtrans: response()->json(['message' => 'OK'])
            else [status lain]
                Webhook->>Transaksi: $transaksi->update(['status' => 'Pending'])
                Webhook-->>Midtrans: response()->json(['message' => 'OK'])
            end
        end
    end
```

## 6. Pembelian dan Stok Obat

```mermaid
sequenceDiagram
    actor Admin
    participant Resource as PembelianObatResource
    participant Detail as PembelianObatDetail
    participant PurchaseStock as PembelianObatStockService
    participant ExpiryStock as StokObatExpiryService
    participant RecipeStock as ResepDetailStockService
    participant Stok as StokObat
    participant Mutasi as StokObatMutasi
    participant Ringkasan as ObatStockSummaryService

    Admin->>Resource: simpan detail pembelian
    Resource->>Detail: create detail
    Detail->>PurchaseStock: applyCreated()
    PurchaseStock->>Stok: firstOrCreate obat + batch + harga beli + kadaluarsa
    PurchaseStock->>Stok: tambah jumlah stok
    PurchaseStock->>Mutasi: catat jumlah_masuk pembelian
    PurchaseStock->>Detail: hitung ulang total pembelian
    PurchaseStock->>Ringkasan: sync(obat_id)
    Ringkasan->>Stok: jumlahkan semua batch
    Ringkasan-->>Resource: stok agregat obat terbaru

    opt koreksi atau hapus detail pembelian
        Admin->>Resource: ubah/hapus detail pembelian
        Resource->>Detail: update/delete detail
        Detail->>PurchaseStock: ensureOriginalStockCanBeReversed()
        alt batch sudah dipakai resep atau stok tidak cukup
            PurchaseStock-->>Admin: kesalahan validasi
        else stok bisa dikoreksi
            PurchaseStock->>Stok: kembalikan stok pembelian lama
            PurchaseStock->>Mutasi: catat koreksi_pembelian
            PurchaseStock->>Ringkasan: sync(obat_id)
        end
    end

    opt pengeluaran resep
        RecipeStock->>Stok: validasi stok tersedia
        RecipeStock->>Stok: kurangi batch FEFO
        RecipeStock->>Mutasi: catat jumlah_keluar resep
        RecipeStock->>Ringkasan: sync(obat_id)
    end

    opt hapus stok kedaluwarsa
        Admin->>ExpiryStock: removeExpired(stokObat)
        alt batch belum kedaluwarsa
            ExpiryStock-->>Admin: kesalahan validasi
        else batch kedaluwarsa
            ExpiryStock->>Stok: set stok menjadi 0
            ExpiryStock->>Mutasi: catat penghapusan_kadaluarsa
            ExpiryStock->>Ringkasan: sync(obat_id)
        end
    end

    Resource-->>Admin: stok obat tersinkron
```

## 7. Administrasi dan Laporan

```mermaid
sequenceDiagram
    actor Admin
    participant Panel as Filament Admin Panel
    participant Resource as Filament Resource
    participant DB as Eloquent/Database
    participant Report as ReportController
    participant PDF as DomPDF

    Admin->>Panel: buka /admin
    Panel->>DB: ambil statistik dan peringatan
    DB-->>Panel: ringkasan operasional
    alt kelola data
        Admin->>Resource: akses, tambah, ubah, atau hapus sesuai fitur resource
        Resource->>DB: validasi dan simpan perubahan
        DB-->>Resource: data terbaru
        Resource-->>Admin: tabel/form terbaru
    else unduh laporan
        Admin->>Report: jenis laporan + rentang tanggal
        Report->>DB: query data keuangan/kunjungan/stok
        DB-->>Report: dataset laporan
        Report->>PDF: render Blade menjadi PDF
        PDF-->>Admin: file laporan
    end
```
