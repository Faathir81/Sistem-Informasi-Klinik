# Sequence Diagram - Sistem Informasi Klinik Ar-Ridlo

Sequence diagram dibuat mengikuti activity diagram utama. Komponen ditampilkan pada level besar agar diagram tetap ringkas untuk laporan.

## 1. Sequence Diagram Autentikasi Pengguna

```mermaid
sequenceDiagram
    actor Pengguna
    participant Web
    participant AuthController
    participant Database
    participant Dashboard

    Pengguna->>Web: Membuka halaman login/register
    alt Register akun baru
        Pengguna->>Web: Mengirim data register
        Web->>AuthController: Validasi data register
        AuthController->>Database: Simpan akun pengguna
        Database-->>AuthController: Akun tersimpan
    end
    Pengguna->>Web: Mengirim email dan password
    Web->>AuthController: Proses login
    AuthController->>Database: Cek akun pengguna
    Database-->>AuthController: Data pengguna
    AuthController->>Dashboard: Redirect sesuai role
    Dashboard-->>Pengguna: Menampilkan dashboard
```

## 2. Sequence Diagram Pengajuan dan Aktivasi Pasien

```mermaid
sequenceDiagram
    actor Pasien
    participant Web
    participant PengajuanController
    participant MidtransService
    participant Midtrans
    participant WebhookController
    participant Database

    Pasien->>Web: Mengisi data pengajuan pasien
    Web->>PengajuanController: Kirim data pengajuan
    PengajuanController->>Database: Simpan pengajuan pasien
    PengajuanController->>MidtransService: Buat transaksi pendaftaran
    MidtransService->>Midtrans: Request Snap pembayaran Rp1.000
    Midtrans-->>MidtransService: Snap token dan URL pembayaran
    MidtransService->>Database: Simpan transaksi pending
    PengajuanController-->>Pasien: Tampilkan halaman pembayaran

    Pasien->>Midtrans: Melakukan pembayaran QRIS
    Midtrans->>WebhookController: Kirim notifikasi pembayaran
    WebhookController->>Database: Validasi order dan update transaksi
    alt Pembayaran settlement
        WebhookController->>Database: Buat data pasien aktif
        WebhookController->>Database: Update status pengajuan disetujui
    else Pembayaran gagal/pending
        WebhookController->>Database: Update status transaksi
    end
    WebhookController-->>Midtrans: Response OK
```

## 3. Sequence Diagram Booking Antrean

```mermaid
sequenceDiagram
    actor Pasien
    participant Web
    participant AntreanController
    participant BookingService
    participant Database
    participant QRCode

    Pasien->>Web: Membuka halaman booking
    Web->>AntreanController: Meminta jadwal dokter
    AntreanController->>Database: Ambil jadwal tersedia
    Database-->>AntreanController: Data jadwal
    AntreanController-->>Web: Tampilkan pilihan jadwal

    Pasien->>Web: Mengirim data booking
    Web->>AntreanController: Proses booking antrean
    AntreanController->>BookingService: Validasi dan buat antrean
    BookingService->>Database: Cek kuota dan nomor antrean
    BookingService->>Database: Simpan antrean
    BookingService-->>AntreanController: Data antrean
    AntreanController->>QRCode: Generate QR antrean
    QRCode-->>AntreanController: QR Code
    AntreanController-->>Pasien: Tampilkan tiket antrean
```

## 4. Sequence Diagram Pemeriksaan dan Resep

```mermaid
sequenceDiagram
    actor Admin
    participant AdminPanel
    participant PemeriksaanResource
    participant ResepResource
    participant StockService
    participant Database

    Admin->>AdminPanel: Membuka data antrean/pemeriksaan
    AdminPanel->>PemeriksaanResource: Input pemeriksaan dan biaya konsultasi
    PemeriksaanResource->>Database: Simpan data pemeriksaan
    Database-->>PemeriksaanResource: Pemeriksaan tersimpan

    opt Resep obat
        Admin->>AdminPanel: Input resep dan detail obat
        AdminPanel->>ResepResource: Simpan resep
        ResepResource->>StockService: Validasi dan hitung stok obat
        StockService->>Database: Update stok dan subtotal resep
        Database-->>StockService: Data berhasil diperbarui
        StockService-->>ResepResource: Total resep terbaru
    end

    PemeriksaanResource-->>Admin: Tampilkan data pemeriksaan
```

## 5. Sequence Diagram Pembayaran Konsultasi

```mermaid
sequenceDiagram
    actor Pasien
    participant Web
    participant PembayaranController
    participant MidtransService
    participant Midtrans
    participant WebhookController
    participant Database

    Pasien->>Web: Membuka halaman pembayaran
    Web->>PembayaranController: Ambil tagihan pemeriksaan
    PembayaranController->>Database: Ambil pemeriksaan dan transaksi
    Database-->>PembayaranController: Data tagihan
    PembayaranController-->>Pasien: Tampilkan tagihan

    Pasien->>Web: Membuat pembayaran QRIS
    Web->>PembayaranController: Kirim nominal pembayaran
    PembayaranController->>MidtransService: Buat transaksi pembayaran
    MidtransService->>Midtrans: Request Snap pembayaran
    Midtrans-->>MidtransService: Snap token dan URL pembayaran
    MidtransService->>Database: Simpan transaksi pending
    PembayaranController-->>Pasien: Tampilkan halaman pembayaran

    Pasien->>Midtrans: Melakukan pembayaran QRIS
    Midtrans->>WebhookController: Kirim notifikasi pembayaran
    WebhookController->>Database: Update status transaksi
    alt Pembayaran settlement
        WebhookController->>Database: Update status bayar pemeriksaan lunas
    end
    WebhookController-->>Midtrans: Response OK
```

## 6. Sequence Diagram Administrasi dan Laporan

```mermaid
sequenceDiagram
    actor Admin
    participant AdminPanel
    participant Resource
    participant ReportController
    participant Database
    participant ExportFile

    Admin->>AdminPanel: Login dan membuka dashboard admin
    AdminPanel->>Database: Ambil ringkasan operasional
    Database-->>AdminPanel: Data dashboard
    AdminPanel-->>Admin: Tampilkan dashboard

    alt Kelola data operasional
        Admin->>AdminPanel: Membuka resource admin
        AdminPanel->>Resource: Tambah/ubah/hapus data
        Resource->>Database: Simpan perubahan
        Database-->>Resource: Perubahan tersimpan
        Resource-->>Admin: Tampilkan data terbaru
    else Melihat laporan
        Admin->>AdminPanel: Membuka halaman laporan
        AdminPanel->>ReportController: Meminta laporan
        ReportController->>Database: Ambil data laporan
        Database-->>ReportController: Data laporan
        ReportController-->>AdminPanel: Tampilkan laporan
        opt Ekspor laporan
            ReportController->>ExportFile: Generate file laporan
            ExportFile-->>Admin: Unduh laporan
        end
    end
```
