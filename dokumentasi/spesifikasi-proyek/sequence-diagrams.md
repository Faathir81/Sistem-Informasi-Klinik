# Sequence Diagram - Sistem Informasi Klinik Ar-Ridlo

Diagram menampilkan komponen aplikasi yang benar-benar terlibat pada tujuh proses utama.

## 1. Autentikasi Pengguna

```mermaid
sequenceDiagram
    actor Pengguna
    participant Web
    participant Auth as AuthenticatedSessionController
    participant Login as LoginRequest
    participant User
    participant Panel as Filament Admin Panel
    participant PasienDash as PasienDashboardController

    Pengguna->>Web: POST /login
    Web->>Auth: email, password
    Auth->>Login: authenticate()
    Login->>User: validasi kredensial
    alt kredensial tidak valid
        Login-->>Pengguna: kesalahan validasi
    else kredensial valid
        Auth->>Web: regenerasi session
        Auth->>User: baca role
        alt role admin
            Auth->>Panel: redirect /admin
            Panel-->>Pengguna: dashboard admin
        else role pasien
            Auth->>PasienDash: redirect /pasien/dashboard
            PasienDash-->>Pengguna: dashboard pasien
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

    Pasien->>Controller: POST data pengajuan
    Controller->>Pengajuan: validasi NIK dan create
    Controller->>Snap: createRegistrationTransaction()
    Snap->>Midtrans: request Snap Rp1.000
    Midtrans-->>Snap: token dan redirect_url
    Snap->>Transaksi: simpan PENDING
    Controller-->>Pasien: halaman pembayaran
    Pasien->>Midtrans: selesaikan pembayaran
    Midtrans->>Webhook: POST notification
    Webhook->>Snap: isValidSignature(payload)
    alt signature tidak valid
        Webhook-->>Midtrans: HTTP 403
    else signature valid dan settlement/capture
        Webhook->>Transaksi: markSettled()
        Transaksi->>Pengajuan: approveFromPayment()
        Pengajuan->>PasienModel: create + nomor rekam medis
        Pengajuan->>Pengajuan: status Disetujui
        Webhook-->>Midtrans: OK
    else expire/cancel/deny/failure
        Webhook->>Transaksi: update status
        Transaksi->>Pengajuan: markPaymentFailed()
        Webhook-->>Midtrans: OK
    end
```

## 3. Booking Antrean

```mermaid
sequenceDiagram
    actor Pasien
    participant Controller as AntreanController
    participant Booking as AntreanBookingService
    participant Jadwal
    participant Libur as JadwalLibur
    participant Antrean
    participant PDF as DomPDF/QR Code

    Pasien->>Controller: GET jadwal dokter dan tanggal
    Controller->>Booking: scheduleAvailability()
    Booking->>Libur: cek libur klinik/dokter
    Booking->>Jadwal: cari sesi sesuai hari
    Booking->>Antrean: hitung sisa kuota
    Booking-->>Controller: jadwal tersedia
    Controller-->>Pasien: pilihan jadwal
    Pasien->>Controller: POST booking
    Controller->>Booking: create(pasien, data)
    Booking->>Jadwal: lock dan validasi sesi
    Booking->>Antrean: cek kuota dan booking duplikat
    Booking->>Antrean: buat nomor dan kode unik
    Antrean-->>Controller: data antrean
    Controller-->>Pasien: tiket antrean
    opt unduh tiket
        Pasien->>Controller: GET tiket PDF
        Controller->>PDF: render tiket dan QR Code
        PDF-->>Pasien: file PDF
    end
```

## 4. Pemeriksaan, Tindakan, dan Resep

```mermaid
sequenceDiagram
    actor Admin
    participant PemeriksaanResource
    participant Pemeriksaan
    participant Tindakan as PemeriksaanTindakan
    participant ResepResource
    participant Resep
    participant Detail as ResepDetail
    participant Stock as ResepDetailStockService
    participant Stok as StokObat
    participant Mutasi as StokObatMutasi

    Admin->>PemeriksaanResource: simpan pemeriksaan
    PemeriksaanResource->>Pemeriksaan: create/update kunjungan, keluhan, diagnosa, status bayar
    opt tindakan medis
        PemeriksaanResource->>Tindakan: simpan layanan, tarif, catatan
    end
    Pemeriksaan->>Pemeriksaan: totalTindakan()
    PemeriksaanResource-->>Admin: pemeriksaan tersimpan

    opt resep obat
        Admin->>ResepResource: pilih pemeriksaan dan simpan resep
        ResepResource->>Resep: create/update resep
        ResepResource->>Detail: create/update obat, jumlah, aturan pakai
        Detail->>Stock: prepareForSave()
        Stock->>Detail: hitung sub_total dari harga jual obat
        Detail->>Stock: reserveForCreate()/applyUpdating()
        Stock->>Stok: validasi stok tersedia dan belum kedaluwarsa
        alt stok tidak mencukupi
            Stock-->>Admin: kesalahan validasi
        else stok cukup
            Stock->>Stok: kurangi batch FEFO
            Stock->>Mutasi: catat pengeluaran resep
            Stock->>Resep: recalculateTotal()
            ResepResource-->>Admin: resep tersimpan
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

    Pasien->>Controller: POST pembayaran pemeriksaan
    Controller->>Pemeriksaan: verifikasi kepemilikan dan ambil rincian
    alt bukan milik pasien
        Controller-->>Pasien: HTTP 403
    else sudah lunas
        Controller-->>Pasien: halaman transaksi lunas
    else belum lunas
    Controller->>Pemeriksaan: hitung konsultasi + tindakan + obat
        alt total kurang dari Rp1.000
            Controller-->>Pasien: kesalahan validasi minimum pembayaran
        else total valid
    Controller->>Snap: createTransaction()
    Snap->>Transaksi: updateOrCreate PENDING
    Snap->>Midtrans: request Snap
    Midtrans-->>Snap: token dan redirect_url
    Controller-->>Pasien: halaman pembayaran
    Pasien->>Midtrans: bayar tagihan
    Midtrans->>Webhook: notifikasi pembayaran
    Webhook->>Snap: validasi signature
            alt signature tidak valid
                Webhook-->>Midtrans: HTTP 403
            else settlement/capture
                Webhook->>Transaksi: markSettled()
                Transaksi->>Pemeriksaan: status_bayar = Lunas
                Webhook-->>Midtrans: OK
            else expire
                Webhook->>Transaksi: status Expire
                Webhook-->>Midtrans: OK
            else cancel/deny/failure
                Webhook->>Transaksi: status Cancel
                Webhook-->>Midtrans: OK
            else status lain
                Webhook->>Transaksi: status Pending
                Webhook-->>Midtrans: OK
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
