# Sequence Diagram - Sistem Informasi Klinik Ar-Ridlo

Diagram menampilkan komponen aplikasi yang benar-benar terlibat pada tujuh proses utama.

## 1. Autentikasi Pengguna

```mermaid
sequenceDiagram
    actor Pengguna
    participant Web
    participant Auth as AuthenticatedSessionController
    participant User
    participant Middleware
    participant Dashboard

    Pengguna->>Web: POST /login
    Web->>Auth: email, password
    Auth->>User: autentikasi kredensial
    alt kredensial tidak valid
        User-->>Pengguna: kesalahan validasi
    else kredensial valid
        Auth->>Web: regenerasi session dan redirect /dashboard
        Web->>Middleware: auth + verified
        Middleware->>User: baca role
        alt role admin
            Middleware->>Dashboard: redirect /admin
        else role pasien
            Middleware->>Dashboard: redirect /pasien/dashboard
        end
        Dashboard-->>Pengguna: dashboard sesuai role
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
    participant Resource as PemeriksaanResource
    participant Pemeriksaan
    participant Tindakan as PemeriksaanTindakan
    participant Resep
    participant Detail as ResepDetail
    participant Stock as ResepDetailStockService
    participant Stok as StokObat
    participant Mutasi as StokObatMutasi

    Admin->>Resource: simpan pemeriksaan
    Resource->>Pemeriksaan: create/update
    opt tindakan medis
        Resource->>Tindakan: simpan layanan, tarif, catatan
    end
    opt resep obat
        Resource->>Resep: simpan resep
        Resource->>Detail: simpan obat, jumlah, aturan pakai
        Detail->>Stock: prepareForSave + reserveForCreate
        Stock->>Stok: validasi stok tersedia
        Stock->>Stok: kurangi batch berurutan FEFO
        Stock->>Mutasi: catat pengeluaran resep
        Stock->>Resep: recalculateTotal()
    end
    Pemeriksaan->>Pemeriksaan: totalTagihan()
    Resource-->>Admin: pemeriksaan tersimpan
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
    Controller->>Pemeriksaan: hitung konsultasi + tindakan + obat
    Controller->>Snap: createTransaction()
    Snap->>Transaksi: updateOrCreate PENDING
    Snap->>Midtrans: request Snap
    Midtrans-->>Snap: token dan redirect_url
    Controller-->>Pasien: halaman pembayaran
    Pasien->>Midtrans: bayar tagihan
    Midtrans->>Webhook: notifikasi pembayaran
    Webhook->>Snap: validasi signature
    Webhook->>Transaksi: markSettled()
    Transaksi->>Pemeriksaan: status_bayar = Lunas
    Webhook-->>Midtrans: OK
```

## 6. Pembelian dan Stok Obat

```mermaid
sequenceDiagram
    actor Admin
    participant Resource as PembelianObatResource
    participant Pembelian as PembelianObatDetail
    participant Service as PembelianObatStockService
    participant Stok as StokObat
    participant Mutasi as StokObatMutasi
    participant Ringkasan as ObatStockSummaryService

    Admin->>Resource: simpan detail pembelian
    Resource->>Pembelian: create detail
    Pembelian->>Service: applyCreated()
    Service->>Stok: firstOrCreate identitas batch
    Service->>Stok: tambah jumlah stok
    Service->>Mutasi: catat jumlah_masuk pembelian
    Service->>Pembelian: hitung ulang total pembelian
    Service->>Ringkasan: sync(obat_id)
    Ringkasan->>Stok: jumlahkan semua batch
    Ringkasan-->>Resource: stok agregat obat terbaru
    Resource-->>Admin: pembelian tersimpan
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
        Admin->>Resource: tambah/ubah/hapus data
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
