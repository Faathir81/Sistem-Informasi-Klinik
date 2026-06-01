# Activity Diagram - Sistem Informasi Klinik Ar-Ridlo

Activity diagram dibuat berdasarkan kelompok proses utama agar tetap mudah dibaca dalam laporan.

## 1. Activity Diagram Autentikasi Pengguna

```mermaid
flowchart TD
    start((Mulai))
    open[Pengguna membuka website]
    choose{Sudah memiliki akun?}
    register[Isi form register]
    validateRegister{Data register valid?}
    createAccount[Sistem membuat akun]
    login[Isi form login]
    validateLogin{Email dan password valid?}
    checkRole{Role pengguna}
    adminDashboard[Masuk dashboard admin]
    patientDashboard[Masuk dashboard pasien]
    logout[Logout]
    finish((Selesai))

    start --> open --> choose
    choose -- Tidak --> register --> validateRegister
    validateRegister -- Tidak --> register
    validateRegister -- Ya --> createAccount --> login
    choose -- Ya --> login
    login --> validateLogin
    validateLogin -- Tidak --> login
    validateLogin -- Ya --> checkRole
    checkRole -- Admin --> adminDashboard
    checkRole -- Pasien --> patientDashboard
    adminDashboard --> logout
    patientDashboard --> logout
    logout --> finish
```

## 2. Activity Diagram Pengajuan dan Aktivasi Pasien

```mermaid
flowchart TD
    start((Mulai))
    login[Pasien login]
    checkPatient{Sudah menjadi pasien aktif?}
    dashboard[Masuk dashboard pasien]
    form[Isi data pengajuan pasien]
    validate{Data pengajuan valid?}
    createSubmission[Sistem menyimpan pengajuan]
    createPayment[Sistem membuat transaksi pendaftaran Rp1.000]
    pay[Pasien melakukan pembayaran QRIS]
    webhook[Midtrans mengirim notifikasi pembayaran]
    paid{Pembayaran sukses?}
    activate[Sistem membuat data pasien dan mengaktifkan akun]
    failed[Sistem menandai pembayaran gagal]
    retry[Pasien mengulang pembayaran/pengajuan]
    finish((Selesai))

    start --> login --> checkPatient
    checkPatient -- Ya --> dashboard --> finish
    checkPatient -- Tidak --> form --> validate
    validate -- Tidak --> form
    validate -- Ya --> createSubmission --> createPayment --> pay --> webhook --> paid
    paid -- Ya --> activate --> dashboard --> finish
    paid -- Tidak --> failed --> retry --> form
```

## 3. Activity Diagram Booking Antrean

```mermaid
flowchart TD
    start((Mulai))
    login[Pasien login]
    checkActive{Data pasien aktif?}
    submission[Ajukan data pasien]
    openBooking[Buka halaman booking antrean]
    chooseDoctor[Pilih dokter]
    chooseDate[Pilih tanggal kunjungan]
    loadSchedule[Sistem menampilkan jadwal tersedia]
    chooseSchedule[Pilih jadwal dokter]
    validateQuota{Jadwal dan kuota tersedia?}
    createQueue[Sistem membuat nomor antrean]
    generateQr[Sistem membuat kode dan QR antrean]
    ticket[Pasien melihat tiket antrean]
    finish((Selesai))

    start --> login --> checkActive
    checkActive -- Tidak --> submission --> finish
    checkActive -- Ya --> openBooking --> chooseDoctor --> chooseDate --> loadSchedule --> chooseSchedule --> validateQuota
    validateQuota -- Tidak --> chooseDoctor
    validateQuota -- Ya --> createQueue --> generateQr --> ticket --> finish
```

## 4. Activity Diagram Pemeriksaan dan Resep

```mermaid
flowchart TD
    start((Mulai))
    adminLogin[Admin login]
    openQueue[Admin membuka data antrean]
    selectPatient[Pilih antrean pasien]
    inputExam[Input pemeriksaan, diagnosa, tindakan, dan biaya konsultasi]
    needRecipe{Perlu resep obat?}
    createExam[Simpan data pemeriksaan]
    inputRecipe[Input resep dan detail obat]
    checkStock{Stok obat cukup?}
    updateStock[Sistem mengurangi stok obat]
    updateTotal[Sistem menghitung total harga obat]
    finishExam[Pemeriksaan tersimpan]
    finish((Selesai))

    start --> adminLogin --> openQueue --> selectPatient --> inputExam --> needRecipe
    needRecipe -- Tidak --> createExam --> finishExam --> finish
    needRecipe -- Ya --> createExam --> inputRecipe --> checkStock
    checkStock -- Tidak --> inputRecipe
    checkStock -- Ya --> updateStock --> updateTotal --> finishExam --> finish
```

## 5. Activity Diagram Pembayaran Konsultasi

```mermaid
flowchart TD
    start((Mulai))
    login[Pasien login]
    openPayment[Buka halaman pembayaran]
    selectBill[Pilih tagihan pemeriksaan]
    inputAmount[Masukkan nominal sesuai tagihan]
    createPayment[Sistem membuat transaksi pembayaran]
    pay[Pasien melakukan pembayaran QRIS]
    webhook[Midtrans mengirim notifikasi pembayaran]
    paid{Pembayaran sukses?}
    markPaid[Sistem menandai tagihan lunas]
    keepPending[Sistem mempertahankan status pending/gagal]
    history[Pasien melihat status pembayaran]
    finish((Selesai))

    start --> login --> openPayment --> selectBill --> inputAmount --> createPayment --> pay --> webhook --> paid
    paid -- Ya --> markPaid --> history --> finish
    paid -- Tidak --> keepPending --> history --> finish
```

## 6. Activity Diagram Administrasi dan Laporan

```mermaid
flowchart TD
    start((Mulai))
    login[Admin login]
    dashboard[Admin membuka dashboard]
    chooseMenu{Pilih menu admin}
    master[Kelola data master]
    schedule[Kelola jadwal dokter]
    queue[Monitor dan atur antrean]
    transaction[Monitor transaksi dan operasional]
    report[Melihat laporan]
    export{Perlu ekspor laporan?}
    exportFile[Ekspor laporan]
    save[Sistem menyimpan perubahan]
    finish((Selesai))

    start --> login --> dashboard --> chooseMenu
    chooseMenu -- Data pasien/dokter/pegawai/obat --> master --> save --> dashboard
    chooseMenu -- Jadwal dokter --> schedule --> save --> dashboard
    chooseMenu -- Antrean --> queue --> save --> dashboard
    chooseMenu -- Transaksi/operasional --> transaction --> dashboard
    chooseMenu -- Laporan --> report --> export
    export -- Ya --> exportFile --> dashboard
    export -- Tidak --> dashboard
    dashboard --> finish
```
