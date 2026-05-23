# Rencana Implementasi: Sistem Informasi Klinik Ar-Ridlo (Skripsi) - HYBRID TALL STACK

Dokumen ini berisi rencana arsitektur, basis data, alur fitur, dan teknologi yang disesuaikan dengan pilihan stack Anda menggunakan versi terbaru yang stabil: **Laravel 12** dan **MySQL 8.0+**, serta keputusan alur bisnis final dan **Pendekatan Hibrida (Filament v4 + Custom Blade)** untuk skripsi Anda.

---

## 🛠️ Pilihan Teknologi Terbaru (Tech Stack)

Berikut adalah detail arsitektur sistem berbasis PHP dan MySQL dengan versi termutakhir dan pendekatan hibrida:

1. **Framework Utama**: **Laravel 12** (PHP 8.3+).
   - *Alasan*: Laravel 12 (rilis Februari 2025) adalah versi paling mutakhir yang kompatibel penuh dengan Filament v4. Menawarkan fitur routing super cepat, manajemen aset modern, dan kompatibilitas penuh dengan PHP 8.3. Sangat menonjol untuk nilai akademis skripsi.
2. **Dashboard Admin**: **Filament PHP v4 (TALL Stack)**.
   - *Alasan*: Versi terbaru Filament (v4) dengan dukungan penuh Laravel 12. Menggunakan kekuatan Livewire v3, Tailwind CSS v4, dan Alpine.js untuk menghasilkan panel admin berkelas industri secara instan. Filament mengotomatisasi pembuatan antarmuka CRUD, grafik statistik, dan tabel interaktif yang sangat responsif dengan estetika visual kelas atas (termasuk Dark Mode bawaan, modal interaktif, dan notifikasi halus).
3. **Dashboard Pasien**: **Custom Blade Templates** dipadukan dengan **Vanilla CSS** modern (menggunakan CSS Nesting & CSS Custom Properties bawaan peramban modern).
   - *Alasan*: Pasien membutuhkan tampilan yang disederhanakan, ramah konsumen, dan khusus (tailor-made) untuk menampilkan bukti antrean QR Code dan input pembayaran QRIS. Pengerjaan kustom ini sekaligus membuktikan kemampuan coding mandiri Anda di hadapan dosen penguji sidang skripsi.
4. **Database**: **MySQL 8.0+**.
   - *Alasan*: RDBMS standar industri termutakhir dengan fitur JSON columns, Window Functions, dan optimasi query mutakhir untuk menangani data medis terstruktur secara tangguh.
5. **Integrasi Pembayaran QRIS**: **Midtrans PHP SDK v2+** & **Midtrans Sandbox API**.
   - *Alasan*: Memungkinkan pengujian transaksi QRIS secara nyata menggunakan simulator pembayaran Midtrans yang aman.
6. **Fitur Pendukung**:
   - **Simple-QRcode (Laravel Package) / qrcode-generator**: Untuk menghasilkan QR Code antrean pasien secara dinamis di Blade template.
   - **Chart.js / Filament Widgets**: Untuk membuat grafik statistik interaktif yang responsif pada dashboard Admin (meliputi status stok obat, visualisasi pengeluaran/pemasukan kas, dan diagram donat obat yang paling banyak digunakan).
   - **Dompdf v3+ / Barryvdh-Dompdf (Laravel Package)**: Untuk mencetak Laporan Pemasukan & Pengeluaran, Laporan Konsultasi, Laporan Stok Obat, serta Slip Gaji individual dalam format PDF resmi.

---

## 📂 Struktur Database & Migrasi (MySQL Schema)

Berikut rancangan tabel basis data relasional (ERD) yang akan diimplementasikan menggunakan **Laravel Migrations**:

### 1. Tabel `users` (Otentikasi & Akun)
* `id` (BIGINT UNSIGNED, Primary Key, Auto Increment)
* `name` (VARCHAR)
* `email` (VARCHAR, Unique)
* `password` (VARCHAR)
* `role` (ENUM: 'admin', 'pasien')
* `no_hp` (VARCHAR)
* `created_at` / `updated_at` (Timestamp)

### 2. Tabel `pasiens` (Data Medis Pasien)
* `id` (BIGINT UNSIGNED, Primary Key, Auto Increment)
* `user_id` (BIGINT UNSIGNED, Nullable, Foreign Key ke `users.id` - Cascade)
* `no_rekam_medis` (VARCHAR, Unique) - Format: *RM-YYYYMMDD-XXXX*
* `nik` (VARCHAR, Unique)
* `nama_pasien` (VARCHAR)
* `tgl_lahir` (DATE)
* `jenis_kelamin` (ENUM: 'Laki-laki', 'Perempuan')
* `alamat` (TEXT)
* `no_hp` (VARCHAR)
* `timestamps`

### 3. Tabel `dokters` (Data Dokter)
* `id` (BIGINT UNSIGNED, Primary Key, Auto Increment)
* `nama_dokter` (VARCHAR)
* `spesialisasi` (VARCHAR)
* `no_hp` (VARCHAR)
* `status_aktif` (BOOLEAN, Default: true)
* `timestamps`

### 4. Tabel `pegawais` (Data Pegawai Non-Dokter)
* `id` (BIGINT UNSIGNED, Primary Key, Auto Increment)
* `nama_pegawai` (VARCHAR)
* `jabatan` (VARCHAR)
* `no_hp` (VARCHAR)
* `timestamps`

### 5. Tabel `jadwal_dokters` (Jadwal Praktek Dokter)
* `id` (BIGINT UNSIGNED, Primary Key, Auto Increment)
* `dokter_id` (BIGINT UNSIGNED, Foreign Key ke `dokters.id` - Cascade)
* `hari` (ENUM: 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')
* `jam_mulai` (TIME)
* `jam_selesai` (TIME)
* `kuota` (INT, Default: 20)
* `timestamps`

### 6. Tabel `antreans` (Pendaftaran Antrean Konsultasi)
* `id` (BIGINT UNSIGNED, Primary Key, Auto Increment)
* `pasien_id` (BIGINT UNSIGNED, Foreign Key ke `pasiens.id`)
* `dokter_id` (BIGINT UNSIGNED, Foreign Key ke `dokters.id`)
* `tgl_kunjungan` (DATE)
* `nomor_antrean` (INT)
* `kode_antrean` (VARCHAR, Unique) - Format: *Q-YYYYMMDD-XXX*
* `status` (ENUM: 'Menunggu', 'Dipanggil', 'Selesai', 'Batal', Default: 'Menunggu')
* `timestamps`

### 7. Tabel `pemeriksaans` (Rekam Medis & Diagnosis)
* `id` (BIGINT UNSIGNED, Primary Key, Auto Increment)
* `antrean_id` (BIGINT UNSIGNED, Unique, Foreign Key ke `antreans.id`)
* `pasien_id` (BIGINT UNSIGNED, Foreign Key ke `pasiens.id`)
* `dokter_id` (BIGINT UNSIGNED, Foreign Key ke `dokters.id`)
* `tgl_pemeriksaan` (DATE)
* `keluhan` (TEXT)
* `diagnosa` (TEXT)
* `tindakan` (TEXT)
* `biaya_konsultasi` (DECIMAL 10,2, Default: 0.00)
* `status_bayar` (ENUM: 'Belum_Bayar', 'Lunas', Default: 'Belum_Bayar')
* `timestamps`

### 8. Tabel `obats` (Stok Obat Klinik)
* `id` (BIGINT UNSIGNED, Primary Key, Auto Increment)
* `nama_obat` (VARCHAR)
* `satuan` (VARCHAR) - Tablet, Botol, Kapsul, dll.
* `stok` (INT)
* `harga_beli` (DECIMAL 10,2)
* `harga_jual` (DECIMAL 10,2)
* `tgl_kadaluarsa` (DATE)
* `timestamps`

### 9. Tabel `reseps` (Resep Obat Utama)
* `id` (BIGINT UNSIGNED, Primary Key, Auto Increment)
* `pemeriksaan_id` (BIGINT UNSIGNED, Unique, Foreign Key ke `pemeriksaans.id` - Cascade)
* `total_harga_obat` (DECIMAL 10,2, Default: 0.00)
* `status_ambil` (ENUM: 'Belum_Diambil', 'Sudah_Diambil', Default: 'Belum_Diambil')
* `timestamps`

### 10. Tabel `resep_details` (Detail Obat dalam Resep)
* `id` (BIGINT UNSIGNED, Primary Key, Auto Increment)
* `resep_id` (BIGINT UNSIGNED, Foreign Key ke `reseps.id` - Cascade)
* `obat_id` (BIGINT UNSIGNED, Foreign Key ke `obats.id`)
* `jumlah` (INT)
* `aturan_pakai` (VARCHAR) - Misal: *3 x 1 tablet sehari*
* `sub_total` (DECIMAL 10,2)
* `timestamps`

### 11. Tabel `transaksis` (Transaksi Keuangan & Midtrans QRIS)
* `id` (BIGINT UNSIGNED, Primary Key, Auto Increment)
* `pemeriksaan_id` (BIGINT UNSIGNED, Unique, Foreign Key ke `pemeriksaans.id` - Cascade)
* `order_id` (VARCHAR, Unique) - ID transaksi eksternal untuk Midtrans
* `amount` (DECIMAL 10,2) - Nilai pembayaran yang diinput manual oleh pasien
* `status` (ENUM: 'PENDING', 'SETTLEMENT', 'EXPIRE', 'CANCEL', Default: 'PENDING')
* `snap_token` (VARCHAR, Nullable)
* `snap_url` (VARCHAR, Nullable)
* `payment_type` (VARCHAR, Nullable)
* `tgl_bayar` (TIMESTAMP, Nullable)
* `timestamps`

### 12. Tabel `pengeluarans` (Operasional & Pembelian Stok - Terpisah dari Gaji)
* `id` (BIGINT UNSIGNED, Primary Key, Auto Increment)
* `deskripsi` (VARCHAR)
* `jumlah` (DECIMAL 10,2)
* `kategori` (ENUM: 'Operasional', 'Pembelian_Obat', 'Lain_Lain')
* `tgl_pengeluaran` (DATE)
* `timestamps`

### 13. Tabel `gajis` (Penggajian Dokter & Pegawai - Terpisah dari Pengeluaran Umum)
* `id` (BIGINT UNSIGNED, Primary Key, Auto Increment)
* `role` (ENUM: 'Dokter', 'Pegawai')
* `dokter_id` (BIGINT UNSIGNED, Nullable, Foreign Key ke `dokters.id`)
* `pegawai_id` (BIGINT UNSIGNED, Nullable, Foreign Key ke `pegawais.id`)
* `bulan_tahun` (VARCHAR) - Format: *YYYY-MM* (misal: *2026-05*)
* `gaji_pokok` (DECIMAL 10,2)
* `tunjangan` (DECIMAL 10,2, Default: 0.00)
* `potongan` (DECIMAL 10,2, Default: 0.00)
* `total_diterima` (DECIMAL 10,2)
* `status_bayar` (ENUM: 'Lunas', 'Pending', Default: 'Lunas')
* `tgl_bayar` (DATE)
* `timestamps`

---

## 💡 Rencana Alur Kerja Fitur & Integrasi (Sesuai Alur Bisnis Anda)

### 1. Peran Aktor & Input Data (Hanya Ada Admin & Pasien)
* **Admin**: Bertindak sebagai operator tunggal klinik. Admin mengelola semua data master (Pasien, Dokter, Pegawai, Jadwal Praktek, dan Stok Obat), mengontrol status antrean, serta menginput Rekam Medis (keluhan, diagnosis, tindakan) dan resep obat setelah konsultasi fisik selesai. **Seluruh fitur operasional Admin diimplementasikan menggunakan Filament Resources & Pages**.
* **Pasien**: Bertindak sebagai pengguna layanan eksternal. Pasien melakukan pendaftaran akun, memesan nomor antrean, melihat riwayat resep/rekam medis diri sendiri, serta melakukan pembayaran QRIS. **Diimplementasikan menggunakan antarmuka Custom Blade + CSS**.

### 2. Alur Pembayaran QRIS (Input Jumlah Manual oleh Pasien)
* **Pasca-Konsultasi**: Admin menyelesaikan proses input rekam medis dan resep di Filament. Nominal tagihan ideal akan tertera di halaman detail riwayat konsultasi pasien sebagai acuan.
* **Proses Bayar**: Pasien mengklik tombol "Bayar via QRIS" pada Custom Blade portal mereka.
* **Input Manual**: Pasien mengetik secara manual jumlah nominal uang yang akan dibayarkan ke dalam kolom input pembayaran.
* **Pemanggilan Midtrans**: Sistem mengambil jumlah nominal yang diketik pasien tersebut, lalu memanggil API Midtrans Snap untuk men-generate barcode QRIS dengan nilai nominal yang persis sama.
* **Notifikasi Webhook**: Setelah scan berhasil diselesaikan, Midtrans mengirimkan webhook ke Laravel untuk mengubah status transaksi tersebut menjadi `SETTLEMENT` (Lunas).

### 3. Pengurangan Stok Obat Real-Time & Notifikasi Stok Kritis
* Begitu Admin menyimpan hasil pemeriksaan medis dan resep pasien di Filament, sistem akan memotong jumlah stok obat secara real-time di tabel `obats`.
* Jika jumlah obat tertentu berada di bawah 10 unit, panel utama dashboard admin Filament akan memunculkan widget atau notifikasi mencolok (stok menipis) agar segera di-restock. Sistem juga memperingatkan jika obat mendekati tanggal kadaluarsa.

### 4. Modul Penggajian & Laporan Keuangan Mandiri (Terpisah)
* **Pembayaran Gaji**: Modul penggajian dokter dan pegawai dikelola secara mandiri menggunakan Filament Custom Resource. Admin dapat menghitung rincian tunjangan/potongan lalu menekan tombol "Bayar". Sistem dapat mencetak Slip Gaji PDF individual.
* **Pemisahan Laporan**: Transaksi penggajian tersimpan secara eksklusif dalam tabel `gajis` dan **tidak dimasukkan secara otomatis** ke tabel `pengeluarans` ataupun Laporan Arus Kas Operasional Umum klinik. Laporan Operasional Umum hanya mencatat pemasukan medis (dari pasien) dan pengeluaran fisik operasional klinik (pembelian obat/ATK, biaya operasional).

---

## 🎨 Desain Estetika Visual Premium (Filament + Custom Blade)
Kita akan menerapkan kombinasi estetika modern:
* **Admin Dashboard (Filament)**: Menggunakan tema bawaan Filament yang mewah dengan gradasi warna gelap-terang, tipografi sans-serif modern, tata letak grid panel responsif, dan layout sidebar modern.
* **Patient Dashboard (Custom Blade)**: Menggunakan konsep **Medical Light Mode** yang bersih dan steril. Kartu-kartu transparan menggunakan efek *glassmorphism* (`backdrop-filter: blur(10px)`), warna utama *Emerald / Mint Green* bergradasi lembut, serta efek micro-animations interaktif.

### Mockup Visual Dashboard Pasien (Portal Booking & Antrean)
Berikut adalah visualisasi rancangan desain **Medical Light Mode** premium yang akan kita bangun untuk portal pasien:

![Patient Dashboard Mockup](/c:/Users/faath/Proyek/Klinik/dokumentasi/patient_dashboard_mockup.png)


