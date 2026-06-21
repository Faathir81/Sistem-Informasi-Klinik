# Spesifikasi Proyek Klinik Ar-Ridlo

Dokumentasi ini menggambarkan implementasi aktual Sistem Informasi Klinik Ar-Ridlo dan dapat dijadikan dasar penyusunan Bab V (Implementasi dan Pengujian). Dokumen diselaraskan dengan codebase pada **21 Juni 2026**.

## Ruang Lingkup Implementasi

Sistem memiliki dua aktor manusia, yaitu **pasien** dan **admin**, serta satu sistem eksternal, yaitu **Midtrans**. Pasien menggunakan antarmuka Laravel Blade, sedangkan admin menggunakan panel Filament pada `/admin`. Keduanya masuk melalui halaman yang sama, `/login`, lalu sistem mengarahkan pengguna berdasarkan nilai `users.role`.

Fungsi utama yang telah diimplementasikan meliputi:

1. autentikasi, verifikasi email, pemulihan kata sandi, dan profil akun;
2. pengajuan dan aktivasi pasien melalui pembayaran pendaftaran Rp1.000;
3. pengelolaan profil pasien dan riwayat medis;
4. booking, pembatalan, QR Code, PDF tiket, dan live preview antrean;
5. pemeriksaan, tindakan medis, resep, serta pembayaran tagihan melalui Midtrans;
6. pengelolaan master data, jadwal dokter, dan jadwal libur;
7. pembelian, stok per batch/harga beli/kedaluwarsa, mutasi, dan pengeluaran resep dengan metode FEFO;
8. transaksi, pengeluaran, payroll, slip gaji, dashboard, dan laporan PDF.

## Teknologi Implementasi

| Komponen | Implementasi |
|---|---|
| Backend | PHP 8.2+, Laravel 12 |
| Admin panel | Filament 4.8.5 |
| Frontend pasien | Blade, Tailwind CSS, Alpine.js, Vite |
| Basis data | Relasional melalui Eloquent ORM dan migration Laravel |
| Pembayaran | Midtrans Snap dan webhook tervalidasi signature |
| Dokumen | DomPDF untuk tiket, slip gaji, dan laporan PDF |
| QR Code | Simple QR Code untuk tiket antrean |

## Daftar Dokumen

| Dokumen | Keterangan |
|---|---|
| [Use Case Diagram](./use-case.md) | Aktor dan fungsi sistem yang tersedia pada antarmuka pasien, admin, dan integrasi Midtrans. |
| [Activity Diagram](./activity-diagrams.md) | Tujuh alur utama dari autentikasi hingga administrasi dan laporan. |
| [Sequence Diagram](./sequence-diagrams.md) | Interaksi komponen untuk tujuh alur utama. |
| [ERD](./erd.md) | Dua puluh satu tabel domain beserta relasi dan kardinalitasnya. |
| [Class Diagram](./class-diagram.md) | Model domain dan service bisnis penting pada implementasi. |

## Pemetaan ke Codebase

| Area | Implementasi utama |
|---|---|
| Routing publik dan pasien | `routes/web.php`, `routes/auth.php` |
| Autentikasi dan otorisasi | `app/Http/Controllers/Auth`, `app/Http/Middleware`, `app/Models/User.php` |
| Portal pasien | `app/Http/Controllers/Pasien`, `resources/views/pasien` |
| Panel admin | `app/Filament/Resources`, `app/Filament/Pages`, `app/Filament/Widgets` |
| Antrean | `AntreanController`, `AntreanBookingService`, `LiveQueuePreviewService` |
| Pembayaran | `PembayaranController`, `MidtransSnapService`, `WebhookController` |
| Persediaan obat | `PembelianObatStockService`, `ResepDetailStockService`, `StokObatExpiryService` |
| Laporan | `LaporanKlinik`, `ReportController`, `resources/views/reports/pdf` |
| Struktur data | `database/migrations`, `app/Models` |

## Batasan Diagram

Tabel teknis Laravel seperti `sessions`, `cache`, `jobs`, `failed_jobs`, `password_reset_tokens`, dan `migrations` tidak dimasukkan ke ERD karena tidak merepresentasikan proses bisnis klinik. Diagram class juga tidak menampilkan seluruh controller, request, middleware, resource Filament, widget, dan view agar tetap terbaca dalam laporan.
