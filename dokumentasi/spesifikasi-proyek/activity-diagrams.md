# Activity Diagram - Sistem Informasi Klinik Ar-Ridlo

Activity diagram berikut menggambarkan tujuh proses utama sesuai implementasi aktual.

## 1. Autentikasi dan Pengalihan Berdasarkan Role

```mermaid
flowchart TD
    start((Mulai)) --> open[Buka /login]
    open --> submit[Masukkan email dan password]
    submit --> valid{Kredensial valid?}
    valid -- Tidak --> error[Tampilkan kesalahan] --> submit
    valid -- Ya --> verify{Email terverifikasi?}
    verify -- Tidak --> notice[Halaman verifikasi email]
    verify -- Ya --> role{Role pengguna}
    role -- admin --> admin[Redirect ke /admin]
    role -- pasien --> pasien[Redirect ke /pasien/dashboard]
    admin --> logout[Logout]
    pasien --> logout
    logout --> finish((Selesai))
```

## 2. Pengajuan dan Aktivasi Pasien

```mermaid
flowchart TD
    start((Mulai)) --> login[Pasien login]
    login --> active{Sudah memiliki profil pasien?}
    active -- Ya --> dashboard[Dashboard pasien] --> finish((Selesai))
    active -- Tidak --> pending{Ada pengajuan menunggu pembayaran?}
    pending -- Ya --> payment[Alihkan ke transaksi yang tersedia]
    pending -- Tidak --> form[Isi identitas pasien]
    form --> valid{Data valid dan NIK belum terdaftar?}
    valid -- Tidak --> form
    valid -- Ya --> submission[Simpan pengajuan Menunggu Pembayaran]
    submission --> snap{Midtrans berhasil membuat Snap Rp1.000?}
    snap -- Tidak --> failed[Tandai Pembayaran Gagal] --> form
    snap -- Ya --> payment
    payment --> pay[Pasien membayar]
    pay --> webhook[Midtrans mengirim webhook]
    webhook --> signature{Signature valid?}
    signature -- Tidak --> reject[Tolak 403] --> finish
    signature -- Ya --> settled{Status SETTLEMENT/CAPTURE?}
    settled -- Tidak --> update[Perbarui Pending/Expire/Cancel] --> finish
    settled -- Ya --> create[Buat pasien dan nomor rekam medis]
    create --> approve[Setujui pengajuan] --> dashboard
```

## 3. Booking dan Pembatalan Antrean

```mermaid
flowchart TD
    start((Mulai)) --> login[Pasien login]
    login --> profile{Memiliki profil pasien aktif?}
    profile -- Tidak --> dashboard[Kembali ke dashboard] --> finish((Selesai))
    profile -- Ya --> choose[Pilih profil pasien, dokter, dan tanggal]
    choose --> schedule[Sistem memuat jadwal]
    schedule --> holiday{Klinik/dokter libur?}
    holiday -- Ya --> choose
    holiday -- Tidak --> select[Pilih sesi praktik]
    select --> validate{Tanggal, hari, sesi, kuota, dan duplikasi valid?}
    validate -- Tidak --> choose
    validate -- Ya --> queue[Buat nomor dan kode antrean unik]
    queue --> ticket[Tampilkan tiket QR]
    ticket --> pdf{Unduh PDF?}
    pdf -- Ya --> download[Hasilkan tiket PDF] --> cancel
    pdf -- Tidak --> cancel{Batalkan antrean?}
    cancel -- Tidak --> finish
    cancel -- Ya --> waiting{Status masih Menunggu?}
    waiting -- Tidak --> error[Tolak pembatalan] --> finish
    waiting -- Ya --> cancelled[Ubah status menjadi Batal] --> finish
```

## 4. Pemeriksaan, Tindakan, dan Resep

```mermaid
flowchart TD
    start((Mulai)) --> login[Admin login]
    login --> queue[Pilih antrean]
    queue --> exam[Input keluhan, diagnosis, dan biaya konsultasi]
    exam --> action{Ada tindakan medis?}
    action -- Ya --> service[Pilih layanan dan tarif tindakan]
    action -- Tidak --> recipe
    service --> recipe{Ada resep?}
    recipe -- Tidak --> total[Hitung total konsultasi + tindakan]
    recipe -- Ya --> detail[Input obat, jumlah, dan aturan pakai]
    detail --> stock{Stok belum kedaluwarsa mencukupi?}
    stock -- Tidak --> detail
    stock -- Ya --> fefo[Kurangi stok batch dengan FEFO]
    fefo --> mutation[Catat mutasi dan hitung subtotal resep]
    mutation --> total[Hitung konsultasi + tindakan + obat]
    total --> save[Simpan pemeriksaan] --> finish((Selesai))
```

## 5. Pembayaran Tagihan Pemeriksaan

```mermaid
flowchart TD
    start((Mulai)) --> list[Buka daftar pembayaran]
    list --> bill[Pilih pemeriksaan milik pasien]
    bill --> settled{Transaksi sudah settlement?}
    settled -- Ya --> paid[Tampilkan tagihan lunas] --> finish((Selesai))
    settled -- Tidak --> amount[Hitung konsultasi + tindakan + obat]
    amount --> minimum{Total minimal Rp1.000?}
    minimum -- Tidak --> error[Tampilkan kesalahan] --> finish
    minimum -- Ya --> snap[Buat/perbarui transaksi Midtrans Snap]
    snap --> pay[Pasien menyelesaikan pembayaran]
    pay --> webhook[Webhook tervalidasi]
    webhook --> status{Status transaksi}
    status -- SETTLEMENT/CAPTURE --> update[Transaksi settlement dan pemeriksaan Lunas]
    status -- EXPIRE --> expire[Transaksi Expire]
    status -- CANCEL/DENY/FAILURE --> cancel[Transaksi Cancel]
    status -- Lainnya --> pending[Transaksi Pending]
    update --> finish
    expire --> finish
    cancel --> finish
    pending --> finish
```

## 6. Pembelian dan Pergerakan Stok Obat

```mermaid
flowchart TD
    start((Mulai)) --> input[Admin input pembelian dan detail obat]
    input --> valid{Obat, batch, harga beli, jumlah, kedaluwarsa valid?}
    valid -- Tidak --> input
    valid -- Ya --> batch[Cari/buat stok berdasarkan obat + batch + harga beli + kedaluwarsa]
    batch --> add[Tambah stok dan catat mutasi pembelian]
    add --> totals[Hitung ulang total pembelian dan ringkasan obat]
    totals --> choice{Proses berikutnya}
    choice -- Resep --> available{Stok belum kedaluwarsa cukup?}
    available -- Tidak --> reject[Tolak detail resep] --> finish((Selesai))
    available -- Ya --> fefo[Ambil batch dengan kedaluwarsa terdekat]
    fefo --> out[Catat mutasi keluar resep] --> finish
    choice -- Hapus stok kedaluwarsa --> expired{Batch sudah kedaluwarsa?}
    expired -- Tidak --> rejectExpiry[Tolak penghapusan] --> finish
    expired -- Ya --> zero[Nolkan stok dan catat mutasi penghapusan] --> finish
```

## 7. Administrasi dan Laporan

```mermaid
flowchart TD
    start((Mulai)) --> login[Admin login]
    login --> dashboard[Dashboard statistik dan peringatan apotek]
    dashboard --> menu{Pilih kelompok menu}
    menu -- Pasien --> patient[Kelola akun, pengajuan, pasien, antrean]
    menu -- Jadwal dan SDM --> hr[Kelola dokter, pegawai, jadwal, libur, gaji]
    menu -- Pelayanan --> medical[Kelola layanan, pemeriksaan, resep]
    menu -- Apotek --> pharmacy[Kelola obat, pembelian, stok batch]
    menu -- Keuangan --> finance[Kelola transaksi dan pengeluaran]
    menu -- Laporan --> range[Pilih jenis dan rentang tanggal]
    patient --> save[Simpan perubahan] --> dashboard
    hr --> save
    medical --> save
    pharmacy --> save
    finance --> save
    range --> valid{Rentang tanggal valid?}
    valid -- Tidak --> range
    valid -- Ya --> type{Jenis laporan}
    type -- Keuangan --> pdf[Generate PDF]
    type -- Kunjungan --> pdf
    type -- Stok obat --> pdf
    pdf --> download[Unduh laporan] --> finish((Selesai))
```
