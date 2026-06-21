# Activity Diagram - Sistem Informasi Klinik Ar-Ridlo

Activity diagram berikut menggunakan **swimlane** untuk memisahkan aktivitas berdasarkan pelakunya. Pada Mermaid, setiap sekat dibuat menggunakan `subgraph`, sedangkan perpindahan proses antar-sekat ditunjukkan oleh panah yang menghubungkan node pada subgraph berbeda.

## 1. Autentikasi dan Pengalihan Berdasarkan Role

```mermaid
flowchart LR
    subgraph penggunaLane["Pengguna"]
        direction TB
        start((Mulai))
        open[Buka halaman login]
        submit[Masukkan email dan password]
        retry[Perbaiki data login]
        notice[Buka halaman verifikasi email]
        adminView[Melihat dashboard admin]
        pasienView[Melihat dashboard pasien]
        logout[Logout]
    end

    subgraph sistemLane["Sistem"]
        direction TB
        valid{Kredensial valid?}
        error[Tampilkan kesalahan]
        verify{Email terverifikasi?}
        role{Role pengguna?}
        admin[Redirect ke /admin]
        pasien[Redirect ke /pasien/dashboard]
        finish((Selesai))
    end

    start --> open --> submit --> valid
    valid -- Tidak --> error --> retry --> submit
    valid -- Ya --> verify
    verify -- Tidak --> notice
    verify -- Ya --> role
    role -- Admin --> admin --> adminView
    role -- Pasien --> pasien --> pasienView
    adminView --> logout --> finish
    pasienView --> logout
```

## 2. Pengajuan dan Aktivasi Pasien

```mermaid
flowchart LR
    subgraph pasienLane["Pasien"]
        direction TB
        start((Mulai))
        login[Login]
        form[Isi identitas pasien]
        payment[Buka transaksi pendaftaran]
        pay[Lakukan pembayaran]
        dashboard[Melihat dashboard pasien]
    end

    subgraph sistemLane["Sistem Klinik"]
        direction TB
        active{Sudah memiliki profil pasien?}
        pending{Ada pengajuan menunggu pembayaran?}
        valid{Data valid dan NIK tersedia?}
        submission[Simpan pengajuan Menunggu Pembayaran]
        failed[Tandai Pembayaran Gagal]
        signature{Signature valid?}
        reject[Tolak webhook HTTP 403]
        settled{Status SETTLEMENT/CAPTURE?}
        update[Perbarui status transaksi]
        create[Buat pasien dan nomor rekam medis]
        approve[Ubah pengajuan menjadi Disetujui]
        finish((Selesai))
    end

    subgraph midtransLane["Midtrans"]
        direction TB
        snap{Berhasil membuat Snap Rp1.000?}
        webhook[Kirim notifikasi webhook]
    end

    start --> login --> active
    active -- Ya --> dashboard --> finish
    active -- Tidak --> pending
    pending -- Ya --> payment
    pending -- Tidak --> form --> valid
    valid -- Tidak --> form
    valid -- Ya --> submission --> snap
    snap -- Tidak --> failed --> form
    snap -- Ya --> payment --> pay --> webhook --> signature
    signature -- Tidak --> reject --> finish
    signature -- Ya --> settled
    settled -- Tidak --> update --> finish
    settled -- Ya --> create --> approve --> dashboard
```

## 3. Booking dan Pembatalan Antrean

```mermaid
flowchart LR
    subgraph pasienLane["Pasien"]
        direction TB
        start((Mulai))
        login[Login]
        choose[Pilih profil pasien, dokter, dan tanggal]
        select[Pilih sesi praktik]
        ticket[Melihat tiket QR]
        pdf{Unduh PDF?}
        download[Unduh tiket PDF]
        cancel{Batalkan antrean?}
    end

    subgraph sistemLane["Sistem"]
        direction TB
        profile{Memiliki profil pasien aktif?}
        dashboard[Kembali ke dashboard]
        schedule[Memuat jadwal dokter]
        holiday{Klinik atau dokter libur?}
        validate{Tanggal, sesi, kuota, dan duplikasi valid?}
        queue[Buat nomor dan kode antrean unik]
        generatePdf[Hasilkan tiket PDF]
        waiting{Status masih Menunggu?}
        error[Tolak pembatalan]
        cancelled[Ubah status menjadi Batal]
        finish((Selesai))
    end

    start --> login --> profile
    profile -- Tidak --> dashboard --> finish
    profile -- Ya --> choose --> schedule --> holiday
    holiday -- Ya --> choose
    holiday -- Tidak --> select --> validate
    validate -- Tidak --> choose
    validate -- Ya --> queue --> ticket --> pdf
    pdf -- Ya --> generatePdf --> download --> cancel
    pdf -- Tidak --> cancel
    cancel -- Tidak --> finish
    cancel -- Ya --> waiting
    waiting -- Tidak --> error --> finish
    waiting -- Ya --> cancelled --> finish
```

## 4. Pemeriksaan, Tindakan, dan Resep

```mermaid
flowchart LR
    subgraph adminLane["Admin"]
        direction TB
        start((Mulai))
        login[Login ke panel admin]
        queue[Pilih antrean pasien]
        exam[Input keluhan, diagnosis, dan biaya konsultasi]
        action{Ada tindakan medis?}
        service[Pilih layanan dan tarif tindakan]
        recipe{Ada resep?}
        detail[Input obat, jumlah, dan aturan pakai]
        revise[Perbaiki detail resep]
    end

    subgraph sistemLane["Sistem"]
        direction TB
        stock{Stok belum kedaluwarsa mencukupi?}
        fefo[Kurangi stok batch dengan FEFO]
        mutation[Catat mutasi dan hitung subtotal resep]
        total[Hitung konsultasi + tindakan + obat]
        save[Simpan pemeriksaan]
        finish((Selesai))
    end

    start --> login --> queue --> exam --> action
    action -- Ya --> service --> recipe
    action -- Tidak --> recipe
    recipe -- Tidak --> total
    recipe -- Ya --> detail --> stock
    stock -- Tidak --> revise --> detail
    stock -- Ya --> fefo --> mutation --> total
    total --> save --> finish
```

## 5. Pembayaran Tagihan Pemeriksaan

```mermaid
flowchart LR
    subgraph pasienLane["Pasien"]
        direction TB
        start((Mulai))
        list[Buka daftar pembayaran]
        bill[Pilih pemeriksaan]
        pay[Lakukan pembayaran]
        statusView[Melihat status pembayaran]
    end

    subgraph sistemLane["Sistem Klinik"]
        direction TB
        owner{Pemeriksaan milik pasien?}
        forbidden[Tolak akses]
        settled{Sudah settlement?}
        paid[Tampilkan tagihan lunas]
        amount[Hitung konsultasi + tindakan + obat]
        minimum{Total minimal Rp1.000?}
        error[Tampilkan kesalahan]
        createSnap[Buat atau perbarui transaksi Pending]
        signature{Signature webhook valid?}
        reject[Tolak webhook HTTP 403]
        status{Status transaksi?}
        update[Settlement dan pemeriksaan Lunas]
        expire[Transaksi Expire]
        cancel[Transaksi Cancel]
        pending[Transaksi Pending]
        finish((Selesai))
    end

    subgraph midtransLane["Midtrans"]
        direction TB
        snap[Buat transaksi Snap]
        webhook[Kirim notifikasi webhook]
    end

    start --> list --> bill --> owner
    owner -- Tidak --> forbidden --> finish
    owner -- Ya --> settled
    settled -- Ya --> paid --> statusView --> finish
    settled -- Tidak --> amount --> minimum
    minimum -- Tidak --> error --> finish
    minimum -- Ya --> createSnap --> snap --> pay --> webhook --> signature
    signature -- Tidak --> reject --> finish
    signature -- Ya --> status
    status -- SETTLEMENT/CAPTURE --> update --> statusView
    status -- EXPIRE --> expire --> statusView
    status -- CANCEL/DENY/FAILURE --> cancel --> statusView
    status -- Lainnya --> pending --> statusView
```

## 6. Pembelian dan Pergerakan Stok Obat

```mermaid
flowchart LR
    subgraph adminLane["Admin"]
        direction TB
        start((Mulai))
        input[Input pembelian dan detail obat]
        revise[Perbaiki data pembelian]
        choice{Pilih proses stok}
        recipe[Input obat pada resep]
        remove[Pilih penghapusan batch]
    end

    subgraph sistemLane["Sistem"]
        direction TB
        valid{Obat, batch, harga, jumlah, dan kedaluwarsa valid?}
        batch[Cari atau buat stok berdasarkan identitas batch]
        add[Tambah stok dan catat mutasi pembelian]
        totals[Hitung total pembelian dan sinkronkan ringkasan obat]
        available{Stok belum kedaluwarsa cukup?}
        reject[Tolak detail resep]
        fefo[Ambil batch berkedaluwarsa terdekat]
        out[Catat mutasi keluar resep]
        expired{Batch sudah kedaluwarsa?}
        rejectExpiry[Tolak penghapusan]
        zero[Nolkan stok dan catat mutasi penghapusan]
        finish((Selesai))
    end

    start --> input --> valid
    valid -- Tidak --> revise --> input
    valid -- Ya --> batch --> add --> totals --> choice
    choice -- Pengeluaran resep --> recipe --> available
    available -- Tidak --> reject --> finish
    available -- Ya --> fefo --> out --> finish
    choice -- Penghapusan kedaluwarsa --> remove --> expired
    expired -- Tidak --> rejectExpiry --> finish
    expired -- Ya --> zero --> finish
```

## 7. Administrasi dan Laporan

```mermaid
flowchart LR
    subgraph adminLane["Admin"]
        direction TB
        start((Mulai))
        login[Login]
        menu{Pilih kelompok menu}
        patient[Kelola akun, pengajuan, pasien, dan antrean]
        hr[Kelola dokter, pegawai, jadwal, libur, dan gaji]
        medical[Kelola layanan, pemeriksaan, dan resep]
        pharmacy[Kelola obat, pembelian, dan stok batch]
        finance[Kelola transaksi dan pengeluaran]
        range[Pilih jenis laporan dan rentang tanggal]
        revise[Perbaiki rentang tanggal]
        download[Unduh laporan PDF]
    end

    subgraph sistemLane["Sistem"]
        direction TB
        dashboard[Tampilkan statistik dan peringatan apotek]
        save[Simpan perubahan]
        valid{Rentang tanggal valid?}
        type{Jenis laporan?}
        financePdf[Susun data laporan keuangan]
        visitPdf[Susun data laporan kunjungan]
        stockPdf[Susun data laporan stok obat]
        pdf[Render laporan dengan DomPDF]
        finish((Selesai))
    end

    start --> login --> dashboard --> menu
    menu -- Pasien --> patient --> save --> dashboard
    menu -- Jadwal dan SDM --> hr --> save
    menu -- Pelayanan --> medical --> save
    menu -- Apotek --> pharmacy --> save
    menu -- Keuangan --> finance --> save
    menu -- Laporan --> range --> valid
    valid -- Tidak --> revise --> range
    valid -- Ya --> type
    type -- Keuangan --> financePdf --> pdf
    type -- Kunjungan --> visitPdf --> pdf
    type -- Stok obat --> stockPdf --> pdf
    pdf --> download --> finish
```

## Catatan Tampilan Mermaid

- `subgraph` berfungsi sebagai sekat atau swimlane.
- `flowchart LR` menempatkan lane dari kiri ke kanan, sedangkan `direction TB` mengarahkan aktivitas di dalam setiap lane dari atas ke bawah.
- Posisi akhir dapat sedikit berbeda antar-renderer Mermaid karena layout dihitung otomatis.
- Untuk hasil skripsi yang benar-benar presisi seperti diagram UML pada contoh, hasil Mermaid dapat diekspor ke SVG lalu dirapikan di draw.io tanpa mengubah alurnya.
