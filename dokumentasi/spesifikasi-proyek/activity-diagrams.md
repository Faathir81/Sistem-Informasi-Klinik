# Activity Diagram - Sistem Informasi Klinik Ar-Ridlo

Activity diagram berikut dibuat dalam bentuk ringkas agar mudah ditempatkan pada laporan skripsi. Seluruh diagram hanya menampilkan alur utama dan keputusan penting, sedangkan detail teknis seperti validasi field, status webhook, dan proses internal service dijelaskan pada sequence diagram.

## 1. Autentikasi dan Pengalihan Berdasarkan Role

```mermaid
flowchart LR
    subgraph penggunaLane["Pengguna"]
        direction TB
        start((Mulai))
        login[Buka login]
        input[Isi email dan password]
        dashboard[Melihat dashboard]
        logout[Logout]
    end

    subgraph sistemLane["Sistem"]
        direction TB
        cek{Login valid?}
        role{Role pengguna}
        admin[Dashboard admin]
        pasien[Dashboard pasien]
        error[Tampilkan pesan gagal]
        finish((Selesai))
    end

    start --> login --> input --> cek
    cek -- Tidak --> error --> input
    cek -- Ya --> role
    role -- Admin --> admin --> dashboard
    role -- Pasien --> pasien --> dashboard
    dashboard --> logout --> finish
```

## 2. Pengajuan dan Aktivasi Pasien

```mermaid
flowchart LR
    subgraph pasienLane["Pasien"]
        direction TB
        start((Mulai))
        login[Login]
        form[Isi pengajuan pasien]
        bayar[Bayar pendaftaran]
        dashboard[Melihat dashboard pasien]
    end

    subgraph sistemLane["Sistem Klinik"]
        direction TB
        cek{Pengajuan dapat diproses?}
        simpan[Simpan pengajuan]
        transaksi[Buat transaksi]
        aktifkan[Aktifkan profil pasien]
        gagal[Tampilkan status gagal]
        finish((Selesai))
    end

    subgraph midtransLane["Midtrans"]
        direction TB
        proses[Proses pembayaran]
        notif[Kirim notifikasi]
    end

    start --> login --> form --> cek
    cek -- Tidak --> form
    cek -- Ya --> simpan --> transaksi --> bayar
    bayar --> proses --> notif --> aktifkan --> dashboard --> finish
    proses -- Gagal --> gagal --> finish
```

## 3. Booking dan Pembatalan Antrean

```mermaid
flowchart LR
    subgraph pasienLane["Pasien"]
        direction TB
        start((Mulai))
        pilih[Pilih jadwal kunjungan]
        tiket[Melihat tiket]
        batal{Batalkan antrean?}
    end

    subgraph sistemLane["Sistem"]
        direction TB
        cek{Jadwal tersedia?}
        buat[Buat antrean]
        ubah[Batalkan antrean]
        pesan[Tampilkan pesan gagal]
        finish((Selesai))
    end

    start --> pilih --> cek
    cek -- Tidak --> pesan --> pilih
    cek -- Ya --> buat --> tiket --> batal
    batal -- Tidak --> finish
    batal -- Ya --> ubah --> finish
```

## 4. Pemeriksaan, Tindakan, dan Resep

```mermaid
flowchart LR
    subgraph adminLane["Admin"]
        direction TB
        start((Mulai))
        pilih[Pilih pasien]
        periksa[Input hasil pemeriksaan]
        resep{Ada resep?}
        obat[Input detail obat]
    end

    subgraph sistemLane["Sistem"]
        direction TB
        simpan[Simpan pemeriksaan]
        stok{Stok cukup?}
        simpanResep[Simpan resep]
        tagihan[Perbarui tagihan pasien]
        finish((Selesai))
    end

    start --> pilih --> periksa --> simpan --> resep
    resep -- Tidak --> tagihan --> finish
    resep -- Ya --> obat --> stok
    stok -- Tidak --> obat
    stok -- Ya --> simpanResep --> tagihan --> finish
```

## 5. Pembayaran Tagihan Pemeriksaan

```mermaid
flowchart LR
    subgraph pasienLane["Pasien"]
        direction TB
        start((Mulai))
        pilih[Pilih tagihan]
        bayar[Lakukan pembayaran]
        status[Melihat status pembayaran]
    end

    subgraph sistemLane["Sistem Klinik"]
        direction TB
        cek{Tagihan dapat dibayar?}
        transaksi[Buat transaksi]
        update[Perbarui status tagihan]
        gagal[Tampilkan pesan gagal]
        finish((Selesai))
    end

    subgraph midtransLane["Midtrans"]
        direction TB
        proses[Proses pembayaran]
        notif[Kirim notifikasi]
    end

    start --> pilih --> cek
    cek -- Tidak --> gagal --> finish
    cek -- Ya --> transaksi --> bayar --> proses --> notif --> update --> status --> finish
    proses -- Gagal --> gagal --> status
```

## 6. Pembelian dan Pergerakan Stok Obat

```mermaid
flowchart LR
    subgraph adminLane["Admin"]
        direction TB
        start((Mulai))
        pilih{Pilih aktivitas stok}
        beli[Input pembelian]
        resep[Input pemakaian resep]
        hapus[Hapus stok kadaluarsa]
    end

    subgraph sistemLane["Sistem"]
        direction TB
        tambah[Tambah stok]
        cek{Stok tersedia?}
        keluar[Kurangi stok]
        kadaluarsa{Sudah kadaluarsa?}
        mutasi[Catat mutasi stok]
        finish((Selesai))
    end

    start --> pilih
    pilih -- Pembelian --> beli --> tambah --> mutasi --> finish
    pilih -- Resep --> resep --> cek
    cek -- Tidak --> finish
    cek -- Ya --> keluar --> mutasi
    pilih -- Kadaluarsa --> hapus --> kadaluarsa
    kadaluarsa -- Tidak --> finish
    kadaluarsa -- Ya --> mutasi
```

## 7. Administrasi dan Laporan

```mermaid
flowchart LR
    subgraph adminLane["Admin"]
        direction TB
        start((Mulai))
        dashboard[Buka panel admin]
        menu{Pilih menu}
        kelola[Kelola data]
        laporan[Buat laporan]
        unduh[Unduh PDF]
    end

    subgraph sistemLane["Sistem"]
        direction TB
        simpan[Simpan perubahan data]
        olah[Olah data laporan]
        pdf[Render laporan PDF]
        finish((Selesai))
    end

    start --> dashboard --> menu
    menu -- Data Klinik --> kelola --> simpan --> dashboard
    menu -- Laporan --> laporan --> olah --> pdf --> unduh --> finish
```

## Catatan Tampilan Mermaid

- Activity diagram dibuat ringkas agar tetap terbaca ketika dimasukkan ke laporan.
- Detail teknis sistem tetap dijelaskan pada sequence diagram.
- Jika diagram masih terlalu lebar, orientasi Mermaid dapat diubah dari `flowchart LR` menjadi `flowchart TB` sebelum diekspor.
