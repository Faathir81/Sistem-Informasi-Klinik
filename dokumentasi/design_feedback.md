# Panduan Peningkatan Desain UI/UX (UI/UX Improvement Guide)
## Sistem Informasi Klinik Ar-Ridlo (Laravel 13 + MySQL 8.0+)

Dokumen ini memuat rekomendasi peningkatan (improvement) estetika visual dan fungsionalitas dari rancangan desain halaman depan (landing page) sementara Klinik Ar-Ridlo. 

> [!NOTE]
> **Catatan Konteks Desain**: Karena draf desain yang Anda berikan baru mencakup **Halaman Depan (Landing Page)** saja, berkas draf tersebut akan kita jadikan **Panduan Identitas Visual (Brand Identity System)** utama kita. Seluruh skema warna (hijau mint/sage, aksen oranye, hijau gelap), tipografi, bayangan (shadow), dan gaya tata letak (rounded/glassmorphism) dari Halaman Depan ini akan diadaptasi secara konsisten ke seluruh halaman dalam sistem (termasuk **Dashboard Pasien** dan **Dashboard Admin**), dengan tetap menyesuaikan secara ketat terhadap kebutuhan fungsional Use Case dan tabel skripsi Anda.

---

## 🎨 1. Peningkatan Estetika Visual (Premium Aesthetics)

Desain draf Anda sudah memiliki fondasi yang sangat baik (pemilihan warna hijau sage/mint, warna aksen oranye, dan layout grid yang bersih). Berikut adalah sentuhan premium untuk meningkatkan impresi dosen penguji:

### A. Konsep Glassmorphism & Transparansi Modern
* **Rekomendasi**: Ubah kartu-kartu statis (seperti form booking dan navbar) menjadi semi-transparan dengan efek blur latar belakang (*glassmorphism*). Ini memberikan kesan premium yang modern.
* **Implementasi CSS**:
  ```css
  .glass-card {
      background: rgba(255, 255, 255, 0.75);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border: 1px solid rgba(255, 255, 255, 0.4);
      box-shadow: 0 8px 32px 0 rgba(148, 163, 184, 0.08);
  }
  ```

### B. Micro-Animations & Interaktivitas Dinamis
Antarmuka yang terasa "hidup" saat disentuh kursor akan mendapat nilai plus dalam ujian sidang skripsi.
* **Service Cards (Hover Effect)**: Saat kursor diarahkan ke kartu layanan (Bekam, Terapi, dll.), kartu harus bergeser ke atas secara halus dengan transisi bayangan yang memikat.
  ```css
  .service-card {
      transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.4s ease;
  }
  .service-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
  }
  ```
* **Floating Background Elements**: Lingkaran/blob hijau pastel di bagian latar belakang *Hero Section* (Image 1) bisa diberikan animasi mengapung lambat agar halaman depan terlihat dinamis.
  ```css
  @keyframes float {
      0% { transform: translateY(0px) rotate(0deg); }
      50% { transform: translateY(-15px) rotate(3deg); }
      100% { transform: translateY(0px) rotate(0deg); }
  }
  .background-blob {
      animation: float 8s ease-in-out infinite;
  }
  ```

### C. Aksen Oranye dengan Efek Glow (Button Styling)
* Tombol oranye aksen Anda (`#FF6B00` atau sejenisnya) sudah sangat mencolok sebagai *Call to Action* (CTA). Tambahkan efek bayangan halus berwarna oranye (*soft orange glow*) saat di-hover agar tombol tersebut terasa membal dan interaktif.
  ```css
  .btn-primary:hover {
      background: linear-gradient(135deg, #ff7e1a 0%, #ff5e00 100%);
      box-shadow: 0 10px 20px rgba(255, 107, 0, 0.35);
  }
  ```

---

## ⚙️ 2. Penyesuaian Fungsionalitas & Keselarasan Ketat dengan Use Case (PENTING!)

> [!WARNING]
> **ATURAN EMAS SIDANG SKRIPSI**: Dosen penguji sangat jeli melihat ketidakcocokan antara diagram dengan antarmuka aplikasi. Jika di halaman UI tertulis **BPJS**, **Bekam**, atau **Home Care**, dosen penguji akan menuntut Anda menunjukkan kode program dan tabel databasenya. Karena use case Anda fokus pada **Konsultasi Umum, Antrean QR Code, dan Pembayaran QRIS**, kita **harus membuang seluruh elemen luar tersebut** dari halaman web nyata Anda.

Berikut adalah langkah penyelarasan desain dengan use case:

### A. Penyederhanaan Daftar Layanan & Fasilitas (Image 2 & 3)
* **Kondisi Desain Sementara**: Mencantumkan layanan seperti Bekam, Terapi Herbal, Saraf, dan Pengurutan Tradisional.
* **Penyelarasan Use Case**: Di sistem basis data kita, data transaksi berfokus pada pemeriksaan medis umum oleh dokter. Maka, di halaman UI nyata kita akan membatasi tampilan layanan menjadi:
  1. **Konsultasi Medis**: Pemeriksaan fisik dan konsultasi umum oleh dokter ahli.
  2. **Farmasi & Apoteker**: Pengambilan obat-obatan resmi klinik berdasarkan resep dokter (sesuai tabel `reseps` & `obats`).
  3. **Manajemen Antrean Digital**: Layanan reservasi antrean jarak jauh yang efisien.
* *Manfaat*: Menghindari pertanyaan penguji tentang mengapa ada modul "Bekam" di UI sementara tidak ada di database dan use case.

### B. Form Booking yang Akurat (Image 4)
* **Kondisi Desain Sementara**: Memiliki kolom "Layanan Poli".
* **Penyelarasan Use Case**: Karena klinik ini bersifat terpadu satu pintu (dokter umum/spesialis terdaftar di tabel `dokters`), input "Layanan Poli" dapat ditiadakan atau dibuat dinamis mengambil spesialisasi dari tabel `dokters` yang ada.
* **Logika Autentikasi**: Pasien wajib login sebelum memesan antrean agar `user_id` tersimpan. Jika belum login, tombol "Booking Sekarang" akan membuka modal login yang cantik.

### C. Pembersihan FAQ dari BPJS dan Home Care (Image 4)
* **Kondisi Desain Sementara**: FAQ membahas BPJS Kesehatan dan Layanan Home Care. Kedua hal ini **tidak ada dalam Use Case Diagram** Anda.
* **Penyelarasan Use Case**: Hapus pertanyaan tersebut dan ganti dengan yang 100% didukung sistem:
  1. **Q: Bagaimana cara mendaftar antrean secara online?**  
     *A: Anda cukup membuat akun di menu "Daftar Akun", masuk ke dashboard, pilih tanggal kunjungan dan dokter pilihan, lalu unduh QR Code bukti antrean Anda.*
  2. **Q: Apakah pembayaran bisa menggunakan QRIS?**  
     *A: Ya. Setelah diperiksa oleh dokter, Anda bisa masuk ke dashboard, mengetik nominal biaya pengobatan secara manual, dan men-scan barcode **QRIS Midtrans** untuk pelunasan otomatis.*

### D. QR Code & Status Real-Time di Dashboard Pasien (Use Case: "Mendapatkan QR Code Antrean")
* Di dalam dashboard pasien kustom, buatlah komponen kartu bukti antrean digital yang mewah.
* Kartu ini harus menonjolkan **QR Code antrean yang bersinar**, dengan sebuah *Live Pulsing Badge* berwarna hijau berkedip di samping status antrean (misal: `● Menunggu Dipanggil` atau `● Dipanggil`). Ini membuktikan secara visual Use Case "Mendapatkan QR Code Antrean" secara dinamis saat demo sidang.

---

## 🛠️ 3. Contoh Implementasi Struktur Menu Navigasi Landing Page (HTML & CSS)

Agar memudahkan transisi ke kode Laravel, struktur kelas pembungkus navigasi dapat dikonfigurasi secara modular:

```html
<!-- Navigasi Utama -->
<nav class="navbar glass-card">
    <div class="logo-area">
        <img src="/img/logo.png" alt="Logo" class="logo-img">
        <span class="logo-text">Klinik Ar-Ridlo</span>
    </div>
    
    <ul class="nav-links">
        <li><a href="#beranda" class="active">Beranda</a></li>
        <li><a href="#layanan">Layanan</a></li>
        <li><a href="#fasilitas">Fasilitas</a></li>
        <li><a href="#booking">Ambil Antrean</a></li>
        <li><a href="#faq">FAQ</a></li>
    </ul>
    
    <div class="auth-buttons">
        @auth
            <!-- Ditampilkan saat user sudah login -->
            <a href="/dashboard" class="btn-dashboard">Dashboard Pasien</a>
        @else
            <!-- Ditampilkan saat user belum login -->
            <a href="/login" class="btn-login">Login</a>
            <a href="/register" class="btn-register">Daftar Akun</a>
        @endauth
    </div>
</nav>
```

Dengan sentuhan-sentuhan detail di atas, sistem informasi Klinik Ar-Ridlo Anda akan terlihat layaknya proyek web komersial profesional berkualitas tinggi di hadapan para dosen penguji!
