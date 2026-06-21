# ERD - Sistem Informasi Klinik Ar-Ridlo

ERD berikut merepresentasikan seluruh tabel domain pada migration saat ini. Kolom `created_at` dan `updated_at` tidak ditulis agar diagram tetap terbaca.

```mermaid
erDiagram
    USERS {
        bigint id PK
        string name
        string email UK
        string password
        enum role "admin, pasien"
        string no_hp "nullable"
        timestamp email_verified_at "nullable"
    }
    PASIENS {
        bigint id PK
        bigint user_id FK "nullable"
        string no_rekam_medis UK
        string nik UK
        string nama_pasien
        date tgl_lahir
        enum jenis_kelamin
        text alamat
        string no_hp
    }
    PENGAJUAN_PASIENS {
        bigint id PK
        bigint user_id FK
        bigint pasien_id FK "nullable"
        bigint reviewed_by FK "nullable"
        string nik
        string nama_pasien
        date tgl_lahir
        enum jenis_kelamin
        text alamat
        string no_hp
        text catatan_pasien "nullable"
        string status
        text alasan_penolakan "nullable"
        timestamp reviewed_at "nullable"
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
        enum hari
        time jam_mulai
        time jam_selesai
        uint kuota
    }
    JADWAL_LIBURS {
        bigint id PK
        bigint dokter_id FK "nullable, seluruh klinik"
        date tanggal
        string keterangan "nullable"
        boolean status_aktif
    }
    ANTREANS {
        bigint id PK
        bigint pasien_id FK
        bigint dokter_id FK
        bigint jadwal_dokter_id FK
        date tanggal_kunjungan
        uint nomor_antrean
        string kode_antrean UK
        enum status
    }
    PEMERIKSAANS {
        bigint id PK
        bigint antrean_id FK "unique"
        bigint pasien_id FK
        bigint dokter_id FK
        date tgl_pemeriksaan
        text keluhan
        text diagnosa
        text tindakan "nullable"
        decimal biaya_konsultasi
        enum status_bayar
    }
    LAYANANS {
        bigint id PK
        string nama_layanan
        decimal tarif_default
        boolean status_aktif
    }
    PEMERIKSAAN_TINDAKANS {
        bigint id PK
        bigint pemeriksaan_id FK
        bigint layanan_id FK "nullable"
        string nama_layanan
        decimal tarif
        text catatan "nullable"
    }
    RESEPS {
        bigint id PK
        bigint pemeriksaan_id FK "unique"
        decimal total_harga_obat
        enum status_ambil
    }
    RESEP_DETAILS {
        bigint id PK
        bigint resep_id FK
        bigint obat_id FK
        uint jumlah
        string aturan_pakai
        decimal sub_total
    }
    OBATS {
        bigint id PK
        string nama_obat
        string satuan
        uint stok "ringkasan semua batch"
        decimal harga_beli "batch tersedia terdekat"
        decimal harga_jual
        date tgl_kadaluarsa "batch tersedia terdekat"
    }
    PEMBELIAN_OBATS {
        bigint id PK
        date tanggal_pembelian
        string supplier "nullable"
        decimal total_pembelian
        text catatan "nullable"
    }
    PEMBELIAN_OBAT_DETAILS {
        bigint id PK
        bigint pembelian_obat_id FK
        bigint obat_id FK
        string batch
        decimal harga_beli
        uint jumlah
        date tgl_kadaluarsa
        decimal sub_total
    }
    STOK_OBATS {
        bigint id PK
        bigint obat_id FK
        string batch
        decimal harga_beli
        uint stok
        date tgl_kadaluarsa
    }
    STOK_OBAT_MUTASIS {
        bigint id PK
        bigint obat_id FK
        bigint stok_obat_id FK "nullable"
        bigint resep_detail_id FK "nullable"
        bigint pembelian_obat_detail_id FK "nullable"
        string tipe
        uint jumlah_masuk
        uint jumlah_keluar
        string batch "nullable"
        date tgl_kadaluarsa "nullable"
        string keterangan "nullable"
    }
    TRANSAKSIS {
        bigint id PK
        bigint pemeriksaan_id FK "nullable, unique"
        bigint pengajuan_pasien_id FK "nullable, unique"
        string order_id UK
        decimal amount
        enum status
        string snap_token "nullable"
        string snap_url "nullable"
        string payment_type "nullable"
        timestamp tgl_bayar "nullable"
    }
    GAJIS {
        bigint id PK
        enum role "Dokter, Pegawai"
        bigint dokter_id FK "nullable"
        bigint pegawai_id FK "nullable"
        string bulan_tahun
        decimal gaji_pokok
        decimal tunjangan
        decimal potongan
        decimal total_diterima
        enum status_bayar
        date tgl_bayar "nullable"
    }
    PENGELUARANS {
        bigint id PK
        string deskripsi
        decimal jumlah
        enum kategori
        date tgl_pengeluaran
    }

    USERS o|--o{ PASIENS : memiliki
    USERS ||--o{ PENGAJUAN_PASIENS : mengajukan
    USERS o|--o{ PENGAJUAN_PASIENS : meninjau
    PASIENS o|--o{ PENGAJUAN_PASIENS : hasil_aktivasi
    DOKTERS ||--o{ JADWAL_DOKTERS : memiliki
    DOKTERS o|--o{ JADWAL_LIBURS : dikecualikan
    PASIENS ||--o{ ANTREANS : mendaftar
    DOKTERS ||--o{ ANTREANS : dituju
    JADWAL_DOKTERS ||--o{ ANTREANS : dipilih
    ANTREANS ||--o| PEMERIKSAANS : menghasilkan
    PASIENS ||--o{ PEMERIKSAANS : menjalani
    DOKTERS ||--o{ PEMERIKSAANS : menangani
    PEMERIKSAANS ||--o{ PEMERIKSAAN_TINDAKANS : memiliki
    LAYANANS o|--o{ PEMERIKSAAN_TINDAKANS : menjadi_referensi
    PEMERIKSAANS ||--o| RESEPS : memiliki
    RESEPS ||--o{ RESEP_DETAILS : memiliki
    OBATS ||--o{ RESEP_DETAILS : diresepkan
    PEMBELIAN_OBATS ||--o{ PEMBELIAN_OBAT_DETAILS : memiliki
    OBATS ||--o{ PEMBELIAN_OBAT_DETAILS : dibeli
    OBATS ||--o{ STOK_OBATS : memiliki_batch
    OBATS ||--o{ STOK_OBAT_MUTASIS : memiliki_mutasi
    STOK_OBATS o|--o{ STOK_OBAT_MUTASIS : dicatat
    RESEP_DETAILS o|--o{ STOK_OBAT_MUTASIS : menyebabkan
    PEMBELIAN_OBAT_DETAILS o|--o{ STOK_OBAT_MUTASIS : menyebabkan
    PEMERIKSAANS o|--o| TRANSAKSIS : ditagihkan
    PENGAJUAN_PASIENS o|--o| TRANSAKSIS : ditagihkan
    DOKTERS o|--o{ GAJIS : menerima
    PEGAWAIS o|--o{ GAJIS : menerima
```

## Aturan Integritas Penting

| Aturan | Implementasi |
|---|---|
| Nomor rekam medis, NIK pasien, kode antrean, dan `order_id` | Unik. |
| Nomor antrean | Unik per dokter, tanggal kunjungan, dan nomor antrean. |
| Pemeriksaan | Maksimal satu untuk setiap antrean. |
| Resep | Maksimal satu untuk setiap pemeriksaan. |
| Transaksi | Maksimal satu per pemeriksaan atau satu per pengajuan pasien. Salah satu relasi digunakan sesuai jenis tagihan. |
| Identitas stok | Unik berdasarkan obat, batch, harga beli, dan tanggal kedaluwarsa. |
| Jadwal libur | `dokter_id = NULL` berarti libur seluruh klinik; nilai dokter tertentu berarti libur dokter tersebut. |
| Tindakan pemeriksaan | Menyimpan salinan nama layanan dan tarif agar histori tidak berubah saat master layanan diperbarui/dihapus. |
| Stok pada `obats` | Ringkasan kompatibilitas; sumber detail persediaan adalah `stok_obats` dan auditnya `stok_obat_mutasis`. |

## Catatan Kardinalitas

Codebase menggunakan relasi `User::pasiens()` sehingga satu akun dapat terhubung ke beberapa profil pasien. Walaupun tersedia pula helper `User::pasien()`, alur portal pasien mengambil koleksi `pasiens` dan memvalidasi kepemilikan berdasarkan seluruh ID profil tersebut.
