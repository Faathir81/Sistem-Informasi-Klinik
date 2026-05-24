# Daftar Tugas: Sistem Informasi Klinik Ar-Ridlo

Rencana kerja terperinci untuk membangun sistem informasi klinik berbasis **Laravel 12 + Filament v4 + MySQL 8.0+** menggunakan pendekatan **Hibrida (Filament Admin + Custom Blade Pasien)**.

> Catatan: stack final mengikuti `roadmap.md`, yaitu Laravel 12 karena kompatibel penuh dengan Filament v4.

- `[x]` **Tahap 1: Inisialisasi Proyek & Database**
  - `[x]` Setup proyek Laravel 12 menggunakan Composer
  - `[x]` Instalasi dan konfigurasi Filament PHP v4 panel admin
  - `[x]` Konfigurasi file `.env` untuk koneksi database MySQL 8.0+
  - `[x]` Buat migrasi database sesuai rancangan ERD
  - `[x]` Buat Model Eloquent beserta relasi-relasinya
  - `[x]` Buat seeders untuk admin default, dokter, pegawai, dan obat-obatan
  - `[x]` Jalankan migrasi dan seeder database

- `[x]` **Tahap 2: Autentikasi & Layout Utama**
  - `[x]` Implementasi Login, Register, dan Logout dengan Laravel Breeze
  - `[x]` Buat middleware role `IsAdmin` dan `IsPasien`
  - `[x]` Setup Blade master layouts untuk portal pasien
  - `[x]` Setup design token premium di `resources/css/app.css`
  - `[x]` Konfigurasi Filament Admin Guard & Login Panel

- `[x]` **Tahap 3: Fitur & Tampilan Pasien**
  - `[x]` Dashboard pasien dengan status antrean, ringkasan akun, riwayat, dan tagihan
  - `[x]` Booking antrean berdasarkan dokter, tanggal, jadwal, dan kuota
  - `[x]` Generate nomor antrean otomatis
  - `[x]` Render QR Code antrean pada tiket digital
  - `[x]` Riwayat medis dan resep pasien
  - `[x]` Pembayaran QRIS Midtrans dengan input nominal manual

- `[x]` **Tahap 4: Fitur & Tampilan Admin**
  - `[x]` Dashboard Admin Filament dengan widget statistik dan alert apotek
  - `[x]` Filament Resource untuk pasien, dokter, pegawai, jadwal dokter
  - `[x]` Modul pemantau antrean pasien di Filament
  - `[x]` Modul input rekam medis dan resep obat
  - `[x]` Resource stok obat dan resep
  - `[x]` Resource transaksi, pengeluaran, dan penggajian
  - `[x]` Cetak slip gaji PDF

- `[x]` **Tahap 5: Pelaporan & Ekspor PDF**
  - `[x]` Laporan pemasukan dan pengeluaran
  - `[x]` Laporan konsultasi dan kunjungan pasien
  - `[x]` Laporan mutasi stok obat klinik
  - `[x]` Template PDF dengan kop Klinik Ar-Ridlo

- `[x]` **Tahap 6: Pengujian & Polishing Visual**
  - `[x]` Uji alur antrean dari daftar ke tiket QR
  - `[x]` Uji alur pemeriksaan, resep, tagihan, dan pembayaran QRIS
  - `[x]` Validasi server-side Laravel pada form utama
  - `[x]` Dashboard analitik admin dan laporan PDF siap demo

- `[x]` **Tahap 7: UI/UX Redesign & Finalisasi Frontend**
  - `[x]` Landing page premium tema Hijau Sage & Oranye
  - `[x]` Redesign halaman Login & Register Breeze
  - `[x]` Redesign layout utama portal pasien dan navigasi responsif
  - `[x]` Poles dashboard pasien, booking antrean, tiket QR, riwayat medis, dan pembayaran QRIS
  - `[x]` Tambahkan micro-interactions, hover states, card elevation, dan visual hierarchy
  - `[x]` Konsistensi identitas visual Filament Admin
  - `[x]` Tambahkan aset hero klinik di `public/images/klinik-hero.png`
  - `[x]` Verifikasi build frontend dengan `npm run build`
