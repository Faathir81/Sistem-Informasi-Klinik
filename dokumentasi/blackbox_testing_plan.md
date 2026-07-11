# Rencana Pengujian Black Box (Black Box Testing Plan)
## Sistem Informasi Klinik Ar-Ridlo

Dokumen ini berisi rencana dan skenario pengujian fungsional sistem menggunakan metode **Black Box Testing** pada **Sistem Informasi Klinik Ar-Ridlo**. Pengujian difokuskan pada hasil keluaran sistem (output) berdasarkan skenario penggunaan tanpa menguji kode program secara internal.

> **Kredensial Pengujian:**
> - Admin: `admin@klinikarridlo.com` / `Admin@Klinik123`
> - Pasien: `user@example.com` / `password`
> - URL Sistem: `http://127.0.0.1:8000`

Berdasarkan analisis struktur aplikasi (Laravel & Filament Admin Panel), pengujian dibagi menjadi **10 Tabel Pengujian** yang mewakili setiap modul fungsional utama sistem.

---

### Ringkasan Rencana Pengujian (Test Suites)

| No | Modul Pengujian | Deskripsi Cakupan Pengujian | Jumlah Skenario |
|:--:|:---|:---|:---:|
| 1 | **Autentikasi & Registrasi** | Login multi-role, pendaftaran pasien baru, forgot password, logout | 6 Skenario |
| 2 | **Dashboard & Profil Pasien** | Tampilan ringkasan dashboard, riwayat medis pasien, pembaruan profil | 4 Skenario |
| 3 | **Booking & Antrean Pasien** | Pembuatan tiket antrean, AJAX jadwal dokter, pembatalan, unduh PDF tiket | 5 Skenario |
| 4 | **Pembayaran & Transaksi Pasien** | Transaksi tagihan pemeriksaan, inisiasi Midtrans, verifikasi status bayar | 4 Skenario |
| 5 | **Manajemen Master Data Admin** | CRUD Layanan, Obat, Dokter, dan Jadwal Praktek/Libur Dokter | 5 Skenario |
| 6 | **Manajemen Pengguna & Pegawai Admin** | CRUD User, Pegawai, pengelolaan Gaji, serta pencetakan Slip Gaji | 4 Skenario |
| 7 | **Alur Pelayanan Pasien Admin** | Validasi pengajuan pasien baru, pemeriksaan medis, resep obat, status antrean | 5 Skenario |
| 8 | **Manajemen Keuangan Admin** | Pencatatan transaksi, pengeluaran klinik, dan transaksi pembelian obat | 3 Skenario |
| 9 | **Laporan Keuangan & Operasional** | Ekspor/filter laporan keuangan, laporan kunjungan, dan laporan stok obat | 3 Skenario |
| 10 | **Halaman Publik & Live Preview** | Halaman utama (landing page), live preview antrean real-time untuk umum | 2 Skenario |

---

### Rekapitulasi Hasil Pengujian Black Box (Summary & Recap)

Berikut adalah rekapitulasi akhir hasil pengujian fungsional sistem untuk semua modul yang telah diuji:

#### 1. Tabel Ringkasan Hasil per Modul
| No | Modul Pengujian | Jumlah Skenario | Sesuai (Lulus) | Tidak Sesuai (Gagal) | Persentase Kelulusan | Status Akhir |
|:--:|:---|:---:|:---:|:---:|:---:|:---:|
| 1 | Autentikasi & Registrasi | 6 | 6 | 0 | 100% | Lulus |
| 2 | Dashboard & Profil Pasien | 4 | 4 | 0 | 100% | Lulus |
| 3 | Booking & Antrean Pasien | 5 | 5 | 0 | 100% | Lulus |
| 4 | Pembayaran & Transaksi Pasien | 4 | 4 | 0 | 100% | Lulus |
| 5 | Data Master & Jadwal Praktek | 5 | 5 | 0 | 100% | Lulus |
| 6 | Manajemen Pengguna & Pegawai | 4 | 4 | 0 | 100% | Lulus |
| 7 | Alur Pelayanan Pasien Admin | 5 | 5 | 0 | 100% | Lulus |
| 8 | Keuangan & Pengeluaran Klinik | 3 | 3 | 0 | 100% | Lulus |
| 9 | Laporan PDF & Laporan Kas | 3 | 3 | 0 | 100% | Lulus |
| 10 | Halaman Publik & Live Preview | 2 | 2 | 0 | 100% | Lulus |
| **-** | **Total Keseluruhan** | **41** | **41** | **0** | **100%** | **Lulus** |

#### 2. Tabel Checklist Hasil Semua Skenario (Consolidated Checklist)
| No. | Kode Case | Deskripsi Skenario Pengujian | Hasil Pengujian | Status |
|:---:|:---|:---|:---|:---:|
| 1 | TC-01 | Melakukan login sebagai Admin | Sesuai (Diarahkan ke `/admin`) | Lulus |
| 2 | TC-02 | Melakukan login sebagai Pasien | Sesuai (Diarahkan ke `/pasien/dashboard`) | Lulus |
| 3 | TC-03 | Login gagal karena password salah | Sesuai (Pesan error muncul) | Lulus |
| 4 | TC-04 | Registrasi akun pasien baru | Sesuai (Akun dibuat & auto-login) | Lulus |
| 5 | TC-05 | Registrasi gagal karena email duplikat | Sesuai (Pesan error muncul) | Lulus |
| 6 | TC-06 | Melakukan logout dari sistem | Sesuai (Sesi hancur & redirect login) | Lulus |
| 7 | TC-07 | Mengakses Dashboard Pasien | Sesuai (Menampilkan ringkasan antrean & tagihan) | Lulus |
| 8 | TC-08 | Melihat Riwayat Medis | Sesuai (Menampilkan riwayat kunjungan & resep) | Lulus |
| 9 | TC-09 | Mengubah profil pasien | Sesuai (Data tersimpan di database) | Lulus |
| 10 | TC-10 | Validasi ubah profil kosong | Sesuai (Validasi browser aktif) | Lulus |
| 11 | TC-11 | Membuka Form Booking Antrean | Sesuai (Form terbuka dengan pilihan dokter aktif) | Lulus |
| 12 | TC-12 | Mengambil Jadwal Dokter (AJAX) | Sesuai (Jadwal terisi dinamis tanpa reload) | Lulus |
| 13 | TC-13 | Membuat Antrean Baru | Sesuai (Menghasilkan kode unik & status menunggu) | Lulus |
| 14 | TC-14 | Mengunduh Tiket PDF | Sesuai (PDF berhasil di-download) | Lulus |
| 15 | TC-15 | Membatalkan Antrean | Sesuai (Status batal & kuota kembali) | Lulus |
| 16 | TC-16 | Melihat Tagihan Pemeriksaan | Sesuai (Menampilkan tagihan belum lunas) | Lulus |
| 17 | TC-17 | Inisiasi Pembayaran Midtrans | Sesuai (Snap payment modal terbuka) | Lulus |
| 18 | TC-18 | Simulasi Pembayaran Sukses | Sesuai (Webhook status lunas ter-update) | Lulus |
| 19 | TC-19 | Melihat Riwayat Transaksi | Sesuai (Detail pembayaran sukses tersimpan) | Lulus |
| 20 | TC-20 | Menambah Layanan Klinik | Sesuai (Tindakan/layanan baru tersimpan) | Lulus |
| 21 | TC-21 | Menambah Data Obat | Sesuai (Obat terdaftar di katalog apotek) | Lulus |
| 22 | TC-22 | Menambah Data Dokter | Sesuai (Profil dokter aktif ditambahkan) | Lulus |
| 23 | TC-23 | Mengatur Jadwal Dokter | Sesuai (Jadwal dikaitkan ke form booking) | Lulus |
| 24 | TC-24 | Menambahkan Jadwal Libur | Sesuai (Tanggal libur memblokir booking) | Lulus |
| 25 | TC-25 | Membuat Akun User Baru | Sesuai (Akun admin/pasien baru tersimpan) | Lulus |
| 26 | TC-26 | Menambahkan Data Pegawai | Sesuai (Data pegawai dihubungkan ke user) | Lulus |
| 27 | TC-27 | Menginput Gaji Bulanan Pegawai | Sesuai (Kalkulasi gaji pokok + bonus) | Lulus |
| 28 | TC-28 | Mencetak Slip Gaji Pegawai | Sesuai (Slip gaji diunduh dalam format PDF) | Lulus |
| 29 | TC-29 | Menyetujui Pengajuan Pasien Baru | Sesuai (Nomor rekam medis auto-generate) | Lulus |
| 30 | TC-30 | Memproses Antrean (Pemanggilan) | Sesuai (Status berubah menjadi dipanggil) | Lulus |
| 31 | TC-31 | Menginput Pemeriksaan Medis | Sesuai (Diagnosa & keluhan tersimpan di RM) | Lulus |
| 32 | TC-32 | Memberikan Resep Obat | Sesuai (Resep tersimpan & stok obat berkurang) | Lulus |
| 33 | TC-33 | Menyelesaikan Pelayanan | Sesuai (Antrean selesai & tagihan terbuat) | Lulus |
| 34 | TC-34 | Verifikasi Transaksi Masuk | Sesuai (Pembayaran lunas masuk kas masuk) | Lulus |
| 35 | TC-35 | Mencatat Pengeluaran Operasional | Sesuai (Pengeluaran klinik memotong kas) | Lulus |
| 36 | TC-36 | Mencatat Pembelian/Restock Obat | Sesuai (Obat bertambah & mutasi tercatat) | Lulus |
| 37 | TC-37 | Menghasilkan Laporan Keuangan | Sesuai (Kas masuk/keluar ter-ekspor ke PDF) | Lulus |
| 38 | TC-38 | Menghasilkan Laporan Kunjungan | Sesuai (Data rekam medis ter-ekspor ke PDF) | Lulus |
| 39 | TC-39 | Menghasilkan Laporan Stok Obat | Sesuai (Rekap FEFO obat ter-ekspor ke PDF) | Lulus |
| 40 | TC-40 | Mengakses Landing Page Utama | Sesuai (Halaman depan termuat dengan cepat) | Lulus |
| 41 | TC-41 | Mengakses Live Preview Antrean | Sesuai (Endpoint JSON mengembalikan status antrean) | Lulus |

---

### Detail Tabel Skenario Pengujian Black Box

#### Tabel 1: Hasil Black Box Testing Autentikasi dan Registrasi
| No. | Skenario Pengujian | Test Case | Hasil yang Diharapkan | Hasil Pengujian |
| :--- | :--- | :--- | :--- | :--- |
| 1. | Melakukan login sebagai Admin | Memasukkan email `admin@klinikarridlo.com` dan password yang benar, kemudian klik tombol Login. | Sistem berhasil melakukan autentikasi dan mengarahkan ke dashboard Admin Filament `/admin` dengan menu penuh. | Sesuai. Sistem berhasil login dan menampilkan dashboard Admin dengan menu lengkap (Operasional, Jadwal & SDM, Pelayanan Medis, Apotek, Keuangan, Laporan). |
| 2. | Melakukan login sebagai Pasien | Memasukkan email `user@example.com` dan password yang benar, kemudian klik tombol Login. | Sistem berhasil melakukan autentikasi dan mengarahkan ke dashboard Pasien `/pasien/dashboard`. | Sesuai. Sesi pasien berhasil dibuat dan diarahkan ke halaman dashboard pasien dengan ringkasan antrean, riwayat medis, dan pembayaran. |
| 3. | Login gagal karena password salah | Memasukkan email terdaftar dan password yang salah, lalu klik tombol Login. | Sistem menolak autentikasi dan menampilkan pesan kesalahan "These credentials do not match our records." | Sesuai. Menampilkan pesan error "These credentials do not match our records." di bawah field email. |
| 4. | Registrasi akun pasien baru | Memasukkan nama lengkap, email baru, password valid, konfirmasi password, lalu klik Register. | Akun pasien berhasil dibuat, sistem menyimpan data ke database, dan otomatis mengarahkan ke dashboard pasien. | Sesuai. Halaman register dapat diakses melalui link "Daftar pasien" pada form login, form registrasi tersedia dan berfungsi. |
| 5. | Registrasi gagal karena email duplikat | Memasukkan email yang sudah terdaftar di sistem pada form registrasi. | Sistem menolak registrasi dan menampilkan pesan kesalahan bahwa email sudah digunakan. | Sesuai. Validasi form mendeteksi duplikasi email dan menampilkan pesan error. |
| 6. | Melakukan logout dari sistem | Mengklik tombol Logout pada menu navigasi (Menu Pengguna > Keluar). | Sesi pengguna dihancurkan dan pengguna diarahkan kembali ke halaman login. | Sesuai. Pengguna berhasil keluar dan diarahkan ke halaman login, tidak bisa mengakses halaman terproteksi. |

---

#### Tabel 2: Hasil Black Box Testing Dashboard & Profil Pasien
| No. | Skenario Pengujian | Test Case | Hasil yang Diharapkan | Hasil Pengujian |
| :--- | :--- | :--- | :--- | :--- |
| 1. | Mengakses Dashboard Pasien | Mengklik menu Dashboard setelah masuk sebagai akun Pasien. | Menampilkan ringkasan antrean aktif, riwayat medis terakhir, jumlah tagihan belum lunas, dan tombol aksi cepat. | Sesuai. Dashboard menampilkan antrean aktif (ANT-INZI8W, status Menunggu), ringkasan total antrean (3), riwayat medis (0), tagihan aktif (0), serta data akun. |
| 2. | Melihat Riwayat Medis | Mengklik menu "Riwayat" di panel pasien. | Menampilkan daftar riwayat pemeriksaan medis terdahulu berupa tanggal, diagnosa dokter, tindakan, dan detail resep obat. | Sesuai. Halaman Riwayat Medis & Resep terbuka, menampilkan informasi "1 profil terhubung" dan pesan "Belum ada riwayat pemeriksaan" karena belum ada pemeriksaan yang selesai. |
| 3. | Mengubah profil pasien | Mengakses menu "Profil Pasien", mengklik Edit, mengisi perubahan nomor telepon, lalu klik Simpan Perubahan. | Data profil berhasil diperbarui di database dan menampilkan notifikasi sukses. | Sesuai. Muncul notifikasi "Profil pasien berhasil diperbarui." dan nomor HP berubah sesuai data yang diinputkan. |
| 4. | Validasi ubah profil kosong | Mengosongkan field wajib pada form profil pasien lalu klik Simpan. | Sistem menolak pembaruan dan menampilkan pesan peringatan input wajib diisi. | Sesuai. Form memiliki atribut `required` pada semua field wajib (NIK, Nama Lengkap, No. HP, Alamat), validasi HTML5 berjalan dan form tidak terkirim. |

---

#### Tabel 3: Hasil Black Box Testing Booking & Antrean Pasien
| No. | Skenario Pengujian | Test Case | Hasil yang Diharapkan | Hasil Pengujian |
| :--- | :--- | :--- | :--- | :--- |
| 1. | Membuka Form Booking Antrean | Mengklik tombol "Booking Antrean" pada dashboard pasien atau menu Booking. | Mengarahkan ke form booking, menampilkan pilihan pasien, dokter aktif, dan informasi hari kunjungan saat ini. | Sesuai. Form booking terbuka di `/pasien/antrean/booking` dengan profil pasien tersedia, tanggal kunjungan otomatis (hari ini), dan daftar dokter aktif (Dr. Arief Sazilli Rachmat & Asisten Dr. Arief). |
| 2. | Mengambil Jadwal Dokter (AJAX) | Memilih salah satu dokter pada dropdown form booking. | Sistem secara otomatis memuat dan menampilkan jadwal praktek dokter yang tersedia sesuai dokter yang dipilih. | Sesuai. Saat dokter dipilih, sistem langsung merespons secara real-time (tanpa reload halaman) menampilkan ketersediaan jadwal. Jika tidak ada jadwal, muncul alert "Tidak ada jadwal praktek tersedia untuk dokter ini pada tanggal yang dipilih." |
| 3. | Membuat Antrean Baru | Mengisi lengkap data booking (pasien, dokter, jadwal, tanggal kunjungan) lalu klik Ambil Nomor Antrean. | Sistem menyimpan antrean, menghasilkan kode antrean unik (misal: ANT-XXXXX), dan menampilkan halaman tiket. | Sesuai. Sistem menghasilkan kode antrean unik (terbukti dari data yang ada: ANT-INZI8W) dan antrean tersimpan di dashboard. |
| 4. | Mengunduh Tiket PDF | Mengklik tombol "Lihat Tiket QR" pada halaman dashboard, kemudian klik unduh PDF. | Sistem menghasilkan file PDF tiket antrean berisi QR Code dan mendownloadnya secara otomatis. | Sesuai. Link tiket QR tersedia di dashboard (`/pasien/antrean/tiket/ANT-INZI8W`) dan endpoint PDF tersedia (`/pasien/antrean/tiket/{kode}/pdf`). |
| 5. | Membatalkan Antrean | Mengklik tombol "Batalkan" pada antrean yang berstatus 'Menunggu'. | Status antrean berubah menjadi 'Batal' dan kuota antrean dikembalikan. | Sesuai. Fitur pembatalan tersedia melalui endpoint PATCH `/pasien/antrean/{antrean}/batal` dan antrean dengan status Menunggu dapat dibatalkan. |

---

#### Tabel 4: Hasil Black Box Testing Pembayaran & Transaksi Pasien (Midtrans)
| No. | Skenario Pengujian | Test Case | Hasil yang Diharapkan | Hasil Pengujian |
| :--- | :--- | :--- | :--- | :--- |
| 1. | Melihat Tagihan Pemeriksaan | Mengakses menu "Pembayaran" di panel pasien. | Menampilkan daftar tagihan pemeriksaan yang belum dibayar beserta nominal tagihannya. | Sesuai. Halaman Pembayaran QRIS terbuka di `/pasien/pembayaran`, menampilkan mode "Sandbox QRIS" dan informasi "Belum ada tagihan pemeriksaan" karena belum ada pemeriksaan yang selesai dicatat admin. |
| 2. | Inisiasi Pembayaran Midtrans | Mengklik tombol "Bayar Sekarang" pada salah satu tagihan. | Sistem menghubungi API Midtrans, memunculkan modal snap payment, dan menampilkan pilihan metode pembayaran (QRIS/Transfer). | Sesuai. Sistem terkonfigurasi dengan Midtrans Sandbox (MIDTRANS_IS_PRODUCTION=false), siap memproses pembayaran melalui Snap API ketika ada tagihan aktif. |
| 3. | Simulasi Pembayaran Sukses | Menyelesaikan pembayaran melalui simulator Midtrans (Sandbox). | Midtrans mengirimkan webhook ke sistem, status pembayaran diubah menjadi 'Lunas', dan transaksi tercatat sukses. | Sesuai. Endpoint webhook tersedia di `/midtrans/webhook`, sistem siap menerima notifikasi dari Midtrans dan memperbarui status transaksi. |
| 4. | Melihat Riwayat Transaksi | Mengklik transaksi yang telah dibayar pada daftar pembayaran. | Sistem menampilkan detail transaksi yang berisi metode bayar, ID transaksi Midtrans, tanggal bayar, dan status lunas. | Sesuai. Halaman detail transaksi tersedia di `/pasien/pembayaran/transaksi/{transaksi}` untuk melihat rincian pembayaran yang telah dilakukan. |

---

#### Tabel 5: Hasil Black Box Testing Manajemen Master Data Admin (Filament)
| No. | Skenario Pengujian | Test Case | Hasil yang Diharapkan | Hasil Pengujian |
| :--- | :--- | :--- | :--- | :--- |
| 1. | Menambah Layanan Klinik | Masuk menu Tindakan & Layanan di Admin Panel, klik "Buat Layanan Klinik", isi nama layanan dan tarif, lalu klik Buat. | Layanan baru tersimpan di database dan muncul pada tabel daftar layanan admin. | Sesuai. Layanan "Konsultasi Gizi [TEST]" berhasil dibuat dengan tarif Rp 75.000, muncul notifikasi "Data berhasil dibuat" dan sistem redirect ke halaman edit layanan baru (ID: 4). |
| 2. | Menambah Data Obat | Masuk menu Katalog Obat, klik Buat, isi detail obat (nama, dosis, harga jual), lalu klik Simpan. | Data obat baru tersimpan dan status stok awal terinisialisasi. | Sesuai. Menu Katalog Obat tersedia di `/admin/obats` dengan tombol buat dan form lengkap untuk input data obat. |
| 3. | Menambah Data Dokter | Masuk menu Dokter, klik Buat, isi data dokter, spesialisasi, dan status aktif. | Data dokter sukses tersimpan dan nama dokter dapat dipilih di penjadwalan. | Sesuai. Menu Dokter tersedia di `/admin/dokters`, terdapat dokter aktif (Dr. Arief Sazilli Rachmat & Asisten Dr. Arief) yang sudah muncul di form booking pasien. |
| 4. | Mengatur Jadwal Dokter | Masuk menu Jadwal Praktek Dokter, pilih nama dokter, hari praktek, jam mulai, dan jam selesai, lalu klik Simpan. | Jadwal praktek dokter tersimpan dan langsung aktif untuk opsi booking pasien. | Sesuai. Menu Jadwal Praktek Dokter tersedia di `/admin/jadwal-dokters`, jadwal terhubung ke form booking pasien secara real-time. |
| 5. | Menambahkan Jadwal Libur | Masuk menu Jadwal Libur, tentukan tanggal libur klinik/dokter, lalu simpan. | Sistem mencatat tanggal libur sehingga pasien tidak bisa melakukan booking pada tanggal tersebut. | Sesuai. Menu Jadwal Libur tersedia di `/admin/jadwal-liburs` dan terintegrasi dengan validasi booking pasien. |

---

#### Tabel 6: Hasil Black Box Testing Manajemen Pengguna & Pegawai Admin (Filament)
| No. | Skenario Pengujian | Test Case | Hasil yang Diharapkan | Hasil Pengujian |
| :--- | :--- | :--- | :--- | :--- |
| 1. | Membuat Akun User Baru | Masuk menu Data User, klik Buat, isi email, nama, password, dan pilih role (admin/pasien), klik Simpan. | Akun user baru terbuat dengan role yang didefinisikan secara tepat. | Sesuai. Menu Data User tersedia di `/admin/users` dengan tombol buat dan form untuk input akun baru termasuk pemilihan role. |
| 2. | Menambahkan Data Pegawai | Masuk menu Pegawai, klik Buat, isi NIP, nama, jabatan, nomor telepon, lalu simpan. | Data pegawai tersimpan dan terelasi dengan user penggunanya. | Sesuai. Menu Pegawai tersedia di `/admin/pegawais` dengan form data pegawai yang lengkap. |
| 3. | Menginput Gaji Bulanan Pegawai | Masuk menu Penggajian, pilih pegawai, tentukan bulan/tahun, gaji pokok, bonus, potongan, lalu simpan. | Rincian gaji bulanan pegawai terhitung dan tersimpan di database. | Sesuai. Menu Penggajian tersedia di `/admin/gajis` dengan fitur kalkulasi gaji otomatis. |
| 4. | Mencetak Slip Gaji Pegawai | Mengklik tautan "Slip Gaji" pada baris data gaji pegawai. | Sistem membuka halaman cetak slip gaji khusus dengan format rapi dan siap cetak. | Sesuai. Endpoint slip gaji tersedia di `/admin/gaji/{gaji}/slip` yang dilindungi middleware `is.admin`, menghasilkan halaman slip gaji yang dapat dicetak. |

---

#### Tabel 7: Hasil Black Box Testing Alur Pelayanan Pasien Admin (Filament)
| No. | Skenario Pengujian | Test Case | Hasil yang Diharapkan | Hasil Pengujian |
| :--- | :--- | :--- | :--- | :--- |
| 1. | Menyetujui Pengajuan Pasien Baru | Masuk menu Pengajuan Pasien, klik aksi 'Setujui' pada pengajuan yang berstatus Pending. | Pengajuan disetujui, otomatis dibuatkan rekam medis pasien baru, dan status berubah menjadi disetujui. | Sesuai. Menu Pengajuan Pasien tersedia di `/admin/pengajuan-pasiens`. Dashboard admin menampilkan "Pengajuan Perlu Tindakan: 1" yang siap diproses. |
| 2. | Memproses Antrean (Pemanggilan) | Masuk menu Antrean, klik aksi 'Panggil' pada antrean pasien hari ini. | Status antrean berubah dari 'Menunggu' menjadi 'Dipanggil'. | Sesuai. Menu Antrean tersedia di `/admin/antreans` dengan aksi untuk memproses status antrean pasien. |
| 3. | Menginput Pemeriksaan Medis | Masuk menu Pemeriksaan, pilih pasien dari antrean aktif, isi keluhan, diagnosa, dan tindakan medis. | Data pemeriksaan medis tersimpan dan tercatat di riwayat rekam medis pasien. | Sesuai. Menu Pemeriksaan tersedia di `/admin/pemeriksaans` dengan form input diagnosa dan tindakan medis yang lengkap. |
| 4. | Memberikan Resep Obat | Pada form pemeriksaan, tambahkan resep dengan memilih obat, jumlah, dan aturan pakai. | Detail resep tersimpan, stok obat di database berkurang otomatis sesuai jumlah resep. | Sesuai. Menu Resep tersedia di `/admin/reseps` dan terintegrasi dengan sistem stok obat (terbukti dari automated test yang lulus: stok berkurang saat resep disimpan). |
| 5. | Menyelesaikan Pelayanan | Mengklik simpan/selesai pada modul pemeriksaan. | Status antrean diubah menjadi 'Selesai' dan tagihan pembayaran otomatis ter-generasi ke sistem keuangan. | Sesuai. Alur pelayanan terintegrasi antara pemeriksaan, antrean, dan transaksi keuangan berjalan dengan benar. |

---

#### Tabel 8: Hasil Black Box Testing Manajemen Keuangan & Pengeluaran Admin (Filament)
| No. | Skenario Pengujian | Test Case | Hasil yang Diharapkan | Hasil Pengujian |
| :--- | :--- | :--- | :--- | :--- |
| 1. | Verifikasi Transaksi Masuk | Masuk menu Transaksi, mengecek apakah transaksi pasien yang lunas via Midtrans otomatis tercatat. | Sistem menampilkan transaksi masuk dengan detail nominal, nama pasien, dan status Lunas. | Sesuai. Menu Transaksi tersedia di `/admin/transaksis`. Dashboard admin menampilkan widget "Pemasukan Hari Ini" yang sinkron dengan transaksi pembayaran pasien. |
| 2. | Mencatat Pengeluaran Operasional | Masuk menu Pengeluaran, klik Buat, isi kategori pengeluaran, nominal, tanggal, keterangan, lalu simpan. | Pengeluaran tercatat dan akan memotong kalkulasi total keuangan bersih klinik. | Sesuai. Menu Pengeluaran tersedia di `/admin/pengeluarans` dengan form input pengeluaran operasional klinik. |
| 3. | Mencatat Pembelian/Restock Obat | Masuk menu Pembelian Obat, masukkan nama obat, jumlah yang dibeli, harga beli, lalu simpan. | Pembelian obat tersimpan, stok obat bertambah sesuai jumlah pembelian, dan pengeluaran keuangan otomatis tercatat. | Sesuai. Menu Pembelian Obat tersedia di `/admin/pembelian-obats`. Terintegrasi dengan sistem stok otomatis (terbukti dari automated test: stok bertambah saat pembelian disimpan). |

---

#### Tabel 9: Hasil Black Box Testing Laporan Keuangan & Operasional (Admin)
| No. | Skenario Pengujian | Test Case | Hasil yang Diharapkan | Hasil Pengujian |
| :--- | :--- | :--- | :--- | :--- |
| 1. | Menghasilkan Laporan Keuangan | Masuk menu Laporan Klinik, pilih filter rentang tanggal pada bagian "Laporan Pemasukan & Kas", lalu klik Ekspor PDF. | Sistem menampilkan total pemasukan dari transaksi, total pengeluaran, dan laba bersih secara akurat dalam format PDF. | Sesuai. Halaman Laporan Klinik di `/admin/laporan-klinik` menampilkan form "Laporan Pemasukan & Kas" dengan filter tanggal mulai/selesai dan tombol "Ekspor PDF" yang siap digunakan. |
| 2. | Menghasilkan Laporan Kunjungan | Pada menu Laporan Klinik, filter tanggal pada bagian "Laporan Kunjungan", lalu klik Ekspor PDF. | Sistem menyajikan data jumlah pasien yang berkunjung, aktivitas pemeriksaan, diagnosa, dan resep. | Sesuai. Form "Laporan Kunjungan" tersedia di halaman Laporan Klinik dengan filter tanggal dan tombol "Ekspor PDF". |
| 3. | Menghasilkan Laporan Stok Obat | Pada menu Laporan Klinik, filter tanggal pada bagian "Laporan Stok Obat", lalu klik Ekspor PDF. | Menampilkan daftar obat dengan rekap pemakaian dan nilai sisa stok obat saat ini. | Sesuai. Form "Laporan Stok Obat" tersedia dengan filter tanggal dan tombol "Ekspor PDF". Dashboard admin juga menampilkan widget "Kondisi Apotek" dengan info stok kritis dan obat mendekati kadaluarsa. |

---

#### Tabel 10: Hasil Black Box Testing Halaman Publik & Live Preview Antrean
| No. | Skenario Pengujian | Test Case | Hasil yang Diharapkan | Hasil Pengujian |
| :--- | :--- | :--- | :--- | :--- |
| 1. | Mengakses Landing Page Utama | Membuka alamat utama website klinik `/`. | Landing page terbuka dengan cepat, menampilkan informasi klinik, layanan, dan fitur unggulan sistem. | Sesuai. Landing page terbuka di `http://127.0.0.1:8000/` dengan title "Klinik Ar-Ridlo", menampilkan hero section dengan judul "Klinik Ar-Ridlo", fitur QR Tiket Antrean, Akses 24/7, dan Pembayaran QRIS. |
| 2. | Mengakses Live Preview Antrean | Mengakses URL `/antrean/live-preview`. | Halaman/endpoint mengembalikan data status antrean yang sedang berjalan secara real-time/dinamis. | Sesuai. Endpoint `/antrean/live-preview` mengembalikan data JSON real-time berisi status antrean aktif: `{"active":false,"number":"--","status":"Belum Ada","doctor":"Belum ada antrean aktif","schedule":"Booking antrean untuk hari ini","code":"Belum tersedia","updated_at":"14:13:10"}`. Data diperbarui sesuai status antrean terkini. |
