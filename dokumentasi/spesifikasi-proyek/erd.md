# ERD - Sistem Informasi Klinik Ar-Ridlo

ERD ini menampilkan tabel domain utama yang digunakan sistem. Tabel teknis bawaan Laravel seperti `sessions`, `cache`, `jobs`, `failed_jobs`, dan `migrations` tidak ditampilkan agar diagram tetap fokus pada proses bisnis klinik.

```mermaid
erDiagram
    USERS {
        bigint id PK
        string name
        string email
        string password
        string role
        string no_hp
        timestamp email_verified_at
    }

    PASIENS {
        bigint id PK
        bigint user_id FK
        string no_rekam_medis
        string nik
        string nama_pasien
        date tgl_lahir
        string jenis_kelamin
        text alamat
        string no_hp
    }

    PENGAJUAN_PASIENS {
        bigint id PK
        bigint user_id FK
        bigint pasien_id FK
        string nik
        string nama_pasien
        date tgl_lahir
        string jenis_kelamin
        text alamat
        string no_hp
        text catatan_pasien
        string status
        timestamp reviewed_at
    }

    DOKTERS {
        bigint id PK
        string nama_dokter
        string spesialisasi
        string no_hp
        boolean status_aktif
    }

    PEGAWAIS {
        bigint id PK
        string nama_pegawai
        string jabatan
        string no_hp
    }

    JADWAL_DOKTERS {
        bigint id PK
        bigint dokter_id FK
        string hari
        time jam_mulai
        time jam_selesai
        integer kuota
    }

    ANTREANS {
        bigint id PK
        bigint pasien_id FK
        bigint dokter_id FK
        bigint jadwal_dokter_id FK
        date tanggal_kunjungan
        integer nomor_antrean
        string kode_antrean
        string status
    }

    PEMERIKSAANS {
        bigint id PK
        bigint antrean_id FK
        bigint pasien_id FK
        bigint dokter_id FK
        date tgl_pemeriksaan
        text keluhan
        text diagnosa
        text tindakan
        decimal biaya_konsultasi
        string status_bayar
    }

    RESEPS {
        bigint id PK
        bigint pemeriksaan_id FK
        decimal total_harga_obat
        string status_ambil
    }

    RESEP_DETAILS {
        bigint id PK
        bigint resep_id FK
        bigint obat_id FK
        integer jumlah
        string aturan_pakai
        decimal sub_total
    }

    OBATS {
        bigint id PK
        string nama_obat
        string satuan
        integer stok
        decimal harga_beli
        decimal harga_jual
        date tgl_kadaluarsa
    }

    TRANSAKSIS {
        bigint id PK
        bigint pemeriksaan_id FK
        bigint pengajuan_pasien_id FK
        string order_id
        decimal amount
        string status
        string payment_type
        timestamp tgl_bayar
    }

    GAJIS {
        bigint id PK
        string role
        bigint dokter_id FK
        bigint pegawai_id FK
        string bulan_tahun
        decimal gaji_pokok
        decimal tunjangan
        decimal potongan
        decimal total_diterima
        string status_bayar
        date tgl_bayar
    }

    PENGELUARANS {
        bigint id PK
        string deskripsi
        decimal jumlah
        string kategori
        date tgl_pengeluaran
    }

    USERS ||--o| PASIENS : memiliki
    USERS ||--o{ PENGAJUAN_PASIENS : mengajukan
    PASIENS ||--o| PENGAJUAN_PASIENS : berasal_dari

    DOKTERS ||--o{ JADWAL_DOKTERS : memiliki
    PASIENS ||--o{ ANTREANS : membuat
    DOKTERS ||--o{ ANTREANS : melayani
    JADWAL_DOKTERS ||--o{ ANTREANS : dipilih

    ANTREANS ||--o| PEMERIKSAANS : menghasilkan
    PASIENS ||--o{ PEMERIKSAANS : menjalani
    DOKTERS ||--o{ PEMERIKSAANS : memeriksa

    PEMERIKSAANS ||--o| RESEPS : memiliki
    RESEPS ||--o{ RESEP_DETAILS : memiliki
    OBATS ||--o{ RESEP_DETAILS : digunakan

    PEMERIKSAANS ||--o| TRANSAKSIS : dibayar
    PENGAJUAN_PASIENS ||--o| TRANSAKSIS : dibayar

    DOKTERS ||--o{ GAJIS : menerima
    PEGAWAIS ||--o{ GAJIS : menerima
```

## Catatan

| Bagian | Keterangan |
|---|---|
| `users` dan `pasiens` | Satu akun pasien dapat memiliki satu data pasien setelah aktivasi berhasil. |
| `pengajuan_pasiens` | Menyimpan data pengajuan pasien sebelum menjadi data pasien aktif. |
| `transaksis` | Dapat terkait ke `pemeriksaans` untuk pembayaran konsultasi atau ke `pengajuan_pasiens` untuk pembayaran pendaftaran. |
| `resep_details` dan `obats` | Detail resep mengurangi stok obat dan membentuk total harga obat. |
| `gajis` | Penerima gaji dapat berupa dokter atau pegawai sesuai nilai `role`. |
