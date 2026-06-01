# Class Diagram - Sistem Informasi Klinik Ar-Ridlo

Class diagram ini menampilkan model domain utama dan service penting yang menjalankan proses bisnis. Class teknis Laravel seperti controller, middleware, migration, request, dan Filament resource tidak ditampilkan agar diagram tetap ringkas untuk laporan.

```mermaid
classDiagram
    class User {
        +id
        +name
        +email
        +role
        +no_hp
        +isAdmin()
        +isPasien()
    }

    class Pasien {
        +id
        +user_id
        +no_rekam_medis
        +nik
        +nama_pasien
        +tgl_lahir
        +jenis_kelamin
        +alamat
        +no_hp
    }

    class PengajuanPasien {
        +id
        +user_id
        +pasien_id
        +nik
        +nama_pasien
        +status
        +approveFromPayment()
        +markPaymentFailed()
        +isMenungguPembayaran()
    }

    class Dokter {
        +id
        +nama_dokter
        +spesialisasi
        +no_hp
        +status_aktif
    }

    class Pegawai {
        +id
        +nama_pegawai
        +jabatan
        +no_hp
    }

    class JadwalDokter {
        +id
        +dokter_id
        +hari
        +jam_mulai
        +jam_selesai
        +kuota
    }

    class Antrean {
        +id
        +pasien_id
        +dokter_id
        +jadwal_dokter_id
        +tanggal_kunjungan
        +nomor_antrean
        +kode_antrean
        +status
    }

    class Pemeriksaan {
        +id
        +antrean_id
        +pasien_id
        +dokter_id
        +tgl_pemeriksaan
        +keluhan
        +diagnosa
        +tindakan
        +biaya_konsultasi
        +status_bayar
        +totalTagihan()
    }

    class Resep {
        +id
        +pemeriksaan_id
        +total_harga_obat
        +status_ambil
        +recalculateTotal()
    }

    class ResepDetail {
        +id
        +resep_id
        +obat_id
        +jumlah
        +aturan_pakai
        +sub_total
    }

    class Obat {
        +id
        +nama_obat
        +satuan
        +stok
        +harga_beli
        +harga_jual
        +tgl_kadaluarsa
        +scopeStokKritis()
        +scopeKadaluarsaSegera()
    }

    class Transaksi {
        +id
        +pemeriksaan_id
        +pengajuan_pasien_id
        +order_id
        +amount
        +status
        +payment_type
        +tgl_bayar
        +markSettled()
    }

    class Gaji {
        +id
        +role
        +dokter_id
        +pegawai_id
        +bulan_tahun
        +total_diterima
        +status_bayar
        +namaPenerima()
    }

    class Pengeluaran {
        +id
        +deskripsi
        +jumlah
        +kategori
        +tgl_pengeluaran
    }

    class AntreanBookingService {
        +book()
        +cancel()
        +availableSchedules()
    }

    class MidtransSnapService {
        +createTransaction()
        +createRegistrationTransaction()
        +isValidSignature()
        +clientKey()
    }

    class MedicalRecordNumberService {
        +next()
    }

    class ResepDetailStockService {
        +prepareForSave()
        +reserveForCreate()
        +applyCreated()
        +applyUpdating()
        +applyUpdated()
        +applyDeleted()
    }

    User "1" --> "0..1" Pasien
    User "1" --> "0..*" PengajuanPasien
    Pasien "1" --> "0..*" Antrean
    Pasien "1" --> "0..*" Pemeriksaan
    Pasien "1" --> "0..1" PengajuanPasien

    Dokter "1" --> "0..*" JadwalDokter
    Dokter "1" --> "0..*" Antrean
    Dokter "1" --> "0..*" Pemeriksaan
    Dokter "1" --> "0..*" Gaji
    Pegawai "1" --> "0..*" Gaji

    JadwalDokter "1" --> "0..*" Antrean
    Antrean "1" --> "0..1" Pemeriksaan
    Pemeriksaan "1" --> "0..1" Resep
    Pemeriksaan "1" --> "0..1" Transaksi
    PengajuanPasien "1" --> "0..1" Transaksi
    Resep "1" --> "1..*" ResepDetail
    Obat "1" --> "0..*" ResepDetail

    AntreanBookingService ..> Antrean
    AntreanBookingService ..> JadwalDokter
    MidtransSnapService ..> Transaksi
    MidtransSnapService ..> PengajuanPasien
    MedicalRecordNumberService ..> Pasien
    ResepDetailStockService ..> ResepDetail
    ResepDetailStockService ..> Obat
```

## Catatan

| Bagian | Keterangan |
|---|---|
| Model | Merepresentasikan entitas utama klinik dan relasi antar data. |
| Service | Merepresentasikan proses bisnis penting yang dipisah dari controller dan model. |
| Controller dan Filament Resource | Tidak ditampilkan karena berperan sebagai lapisan antarmuka/aplikasi, bukan domain inti. |
