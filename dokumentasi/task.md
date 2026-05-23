# Daftar Tugas: Sistem Informasi Klinik Ar-Ridlo (Laravel 13 + MySQL 8.0+)

Rencana kerja terperinci untuk membangun sistem informasi klinik berbasis Laravel 13 dan MySQL 8.0+ menggunakan pendekatan **Hibrida (Filament + Custom Blade)** yang siap pakai untuk sidang skripsi.

- `[ ]` **Tahap 1: Inisialisasi Proyek & Database**
  - `[ ]` Setup proyek Laravel 13 menggunakan Composer
  - `[ ]` Instalasi dan konfigurasi **Filament PHP** panel admin
  - [ ] Konfigurasi file `.env` untuk koneksi database MySQL 8.0+
  - `[ ]` Buat berkas-berkas Migrasi Database MySQL sesuai rancangan ERD
  - `[ ]` Buat Model Eloquent beserta relasi-relasinya (User, Pasien, Dokter, Pegawai, JadwalDokter, Antrean, Pemeriksaan, Obat, Resep, ResepDetail, Transaksi, Pengeluaran, Gaji)
  - `[ ]` Buat Database Seeders untuk memasukkan data awal (admin default, daftar dokter, pegawai, dan obat-obatan)
  - `[ ]` Jalankan migrasi dan seeder database

- `[ ]` **Tahap 2: Autentikasi & Layout Utama**
  - `[ ]` Implementasi fitur Login, Register, dan Logout dengan Laravel Session (Pasien)
  - `[ ]` Buat custom Middleware untuk membatasi akses berdasarkan peran (`IsAdmin`, `IsPasien`)
  - `[ ]` Setup Blade master layouts dengan gaya *Medical Light Theme* yang responsif (Pasien)
  - `[ ]` Buat file CSS kustom (`public/css/style.css`) berisi design token premium, glassmorphism, dan micro-animations menggunakan CSS Nesting bawaan modern (Pasien)
  - [ ] Konfigurasi Filament Admin Guard & Login Panel (Admin)

- `[ ]` **Tahap 3: Fitur & Tampilan Pasien (Custom Blade)**
  - `[ ]` Halaman Dashboard Pasien (menampilkan jadwal dokter aktif hari ini, status antrean saat ini, widget info cepat)
  - `[ ]` Halaman Booking Antrean (pilih dokter, hari/jam, input data jika pertama kali, dan generate nomor urut otomatis)
  - `[ ]` Integrasi Generator QR Code Antrean (kode unik antrean dirender menjadi QR Code dinamis)
  - `[ ]` Halaman Riwayat Medis & Resep Pasien
  - `[ ]` Halaman Pembayaran QRIS Midtrans (menampilkan tagihan pemeriksaan & resep, input nominal bayar manual, integrasi Midtrans Snap JS, serta simulator webhook lokal offline untuk demo sidang)

- `[ ]` **Tahap 4: Fitur & Tampilan Admin (Filament PHP)**
  - `[ ]` Dashboard Admin Filament (Visualisasi statistik transaksi keuangan, bagan diagram donat obat terlaris menggunakan Filament Widgets, alert stok menipis)
  - `[ ]` Filament Resource untuk Kelola Akun & Kelola Data Pasien
  - `[ ]` Filament Resource untuk Kelola Data Dokter, Pegawai, & Jadwal Praktek Dokter
  - `[ ]` Modul Pemantau Antrean Pasien di Filament (panggil nomor antrean, perbarui status 'Dipanggil' / 'Selesai' / 'Batal' menggunakan Filament Custom Actions)
  - `[ ]` Modul Input Rekam Medis & Resep Obat di Filament (input diagnosis, keluhan, biaya konsultasi, obat dan dosis setelah pemeriksaan selesai)
  - `[ ]` Filament Resource untuk Kelola Stok Obat (Apotek) & Kelola Resep Obat
  - `[ ]` Modul Monitoring Transaksi Keuangan di Filament (mencatat semua invoice masuk dan transaksi QRIS)
  - `[ ]` Modul Penggajian Dokter & Pegawai di Filament (perhitungan gaji dan tombol cetak slip gaji format PDF)

- `[ ]` **Tahap 5: Pelaporan & Ekspor PDF (Laravel Controller & Dompdf)**
  - `[ ]` Laporan Pemasukan & Pengeluaran (saringan tanggal, ekspor file PDF via `dompdf` dengan kop klinik resmi)
  - `[ ]` Laporan Konsultasi & Kunjungan Pasien
  - `[ ]` Laporan Mutasi Stok Obat Klinik

- `[ ]` **Tahap 6: Pengujian & Polishing Visual**
  - `[ ]` Uji alur antrean dari daftar -> periksa -> bayar QRIS secara end-to-end
  - `[ ]` Pastikan seluruh form input memiliki validasi server-side Laravel dan pesan error yang ramah pengguna
  - `[ ]` Tulis dokumen `walkthrough.md` berisi rangkuman fitur yang berhasil diselesaikan beserta petunjuk cara menjalankannya
