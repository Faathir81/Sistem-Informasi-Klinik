# Class Diagram - Sistem Informasi Klinik Ar-Ridlo

Diagram memuat model domain dan service yang menjalankan aturan bisnis utama. Controller, request, middleware, resource Filament, widget, dan view tidak ditampilkan.

```mermaid
classDiagram
    class User {
        +id
        +name
        +email
        +role
        +isAdmin() bool
        +isPasien() bool
        +canAccessPanel() bool
    }
    class Pasien {
        +id
        +user_id
        +no_rekam_medis
        +nik
        +nama_pasien
    }
    class PengajuanPasien {
        +id
        +user_id
        +pasien_id
        +status
        +approveFromPayment() Pasien
        +markPaymentFailed()
        +isMenungguPembayaran() bool
    }
    class Dokter {
        +id
        +nama_dokter
        +spesialisasi
        +status_aktif
    }
    class Pegawai {
        +id
        +nama_pegawai
        +jabatan
    }
    class JadwalDokter {
        +id
        +dokter_id
        +hari
        +jam_mulai
        +jam_selesai
        +kuota
    }
    class JadwalLibur {
        +id
        +dokter_id
        +tanggal
        +keterangan
        +status_aktif
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
        +biaya_konsultasi
        +status_bayar
        +totalTindakan() float
        +totalTagihan() float
    }
    class Layanan {
        +id
        +nama_layanan
        +tarif_default
        +status_aktif
    }
    class PemeriksaanTindakan {
        +id
        +pemeriksaan_id
        +layanan_id
        +nama_layanan
        +tarif
        +catatan
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
        +harga_jual
        +totalStok() int
        +stokTersedia() int
        +scopeStokKritis()
        +scopeKadaluarsaSegera()
    }
    class PembelianObat {
        +id
        +tanggal_pembelian
        +supplier
        +total_pembelian
        +recalculateTotal()
    }
    class PembelianObatDetail {
        +id
        +pembelian_obat_id
        +obat_id
        +batch
        +harga_beli
        +jumlah
        +tgl_kadaluarsa
        +sub_total
    }
    class StokObat {
        +id
        +obat_id
        +batch
        +harga_beli
        +stok
        +tgl_kadaluarsa
        +scopeTersedia()
        +scopeKadaluarsa()
        +isExpired() bool
    }
    class StokObatMutasi {
        +id
        +obat_id
        +stok_obat_id
        +resep_detail_id
        +pembelian_obat_detail_id
        +tipe
        +jumlah_masuk
        +jumlah_keluar
    }
    class Transaksi {
        +id
        +pemeriksaan_id
        +pengajuan_pasien_id
        +order_id
        +amount
        +status
        +markSettled()
    }
    class Gaji {
        +id
        +role
        +dokter_id
        +pegawai_id
        +total_diterima
        +status_bayar
        +namaPenerima() string
    }
    class Pengeluaran {
        +id
        +deskripsi
        +jumlah
        +kategori
        +tgl_pengeluaran
    }

    class MedicalRecordNumberService {
        +next() string
    }
    class AntreanBookingService {
        +scheduleAvailability() array
        +availableSchedules()
        +create() Antrean
    }
    class LiveQueuePreviewService {
        +current() Antrean
        +payload() array
        +maskQueueCode() string
    }
    class MidtransSnapService {
        +REGISTRATION_FEE int
        +createTransaction() Transaksi
        +createRegistrationTransaction() Transaksi
        +isValidSignature() bool
        +clientKey() string
    }
    class ResepDetailStockService {
        +prepareForSave()
        +reserveForCreate()
        +applyCreated()
        +applyUpdating()
        +applyDeleted()
    }
    class PembelianObatStockService {
        +applyCreated()
        +applyUpdating()
        +applyDeleting()
    }
    class ObatStockSummaryService {
        +sync()
    }
    class StokObatExpiryService {
        +removeExpired()
    }

    User "1" --> "0..*" Pasien
    User "1" --> "0..*" PengajuanPasien
    Pasien "1" --> "0..*" Antrean
    Pasien "1" --> "0..*" Pemeriksaan
    PengajuanPasien "0..1" --> "0..1" Pasien
    Dokter "1" --> "0..*" JadwalDokter
    Dokter "0..1" --> "0..*" JadwalLibur
    Dokter "1" --> "0..*" Antrean
    Dokter "1" --> "0..*" Pemeriksaan
    JadwalDokter "1" --> "0..*" Antrean
    Antrean "1" --> "0..1" Pemeriksaan
    Pemeriksaan "1" --> "0..*" PemeriksaanTindakan
    Layanan "0..1" --> "0..*" PemeriksaanTindakan
    Pemeriksaan "1" --> "0..1" Resep
    Pemeriksaan "0..1" --> "0..1" Transaksi
    PengajuanPasien "0..1" --> "0..1" Transaksi
    Resep "1" --> "0..*" ResepDetail
    Obat "1" --> "0..*" ResepDetail
    PembelianObat "1" --> "0..*" PembelianObatDetail
    Obat "1" --> "0..*" PembelianObatDetail
    Obat "1" --> "0..*" StokObat
    Obat "1" --> "0..*" StokObatMutasi
    StokObat "0..1" --> "0..*" StokObatMutasi
    Dokter "0..1" --> "0..*" Gaji
    Pegawai "0..1" --> "0..*" Gaji

    MedicalRecordNumberService ..> Pasien : membuat nomor RM
    AntreanBookingService ..> JadwalLibur : memvalidasi
    AntreanBookingService ..> JadwalDokter : memvalidasi
    AntreanBookingService ..> Antrean : membuat
    LiveQueuePreviewService ..> Antrean : membaca
    MidtransSnapService ..> Transaksi : membuat
    ResepDetailStockService ..> StokObat : FEFO
    ResepDetailStockService ..> StokObatMutasi : mencatat
    PembelianObatStockService ..> StokObat : menambah
    PembelianObatStockService ..> StokObatMutasi : mencatat
    ObatStockSummaryService ..> Obat : menyinkronkan
    StokObatExpiryService ..> StokObat : menolkan
```

## Tanggung Jawab Lapisan

| Lapisan | Tanggung jawab |
|---|---|
| Model | Struktur data, relasi Eloquent, cast, agregasi domain, dan event model. |
| Service | Transaksi bisnis yang melibatkan validasi, locking, integrasi eksternal, atau beberapa model. |
| Controller/Request | Otorisasi kepemilikan, validasi input HTTP, orkestrasi service, dan response. |
| Filament Resource | Antarmuka CRUD dan aksi operasional admin. |
| View | Presentasi portal pasien, panel admin, tiket, slip, dan laporan. |

## Aturan Bisnis yang Diwakili

- `Pemeriksaan::totalTagihan()` menjumlahkan konsultasi, seluruh tindakan, dan total resep.
- `Transaksi::markSettled()` melunasi pemeriksaan atau memicu aktivasi pengajuan pasien.
- `ResepDetailStockService` mengeluarkan stok belum kedaluwarsa dengan urutan FEFO dan mencatat mutasi per batch.
- `PembelianObatStockService` menambah stok berdasarkan identitas batch sekaligus menghitung ulang total pembelian.
- `ObatStockSummaryService` menjaga kolom ringkasan pada `obats` tetap sesuai dengan detail `stok_obats`.
