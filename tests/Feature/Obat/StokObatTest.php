<?php

namespace Tests\Feature\Obat;

use App\Models\Antrean;
use App\Models\Dokter;
use App\Models\JadwalDokter;
use App\Models\Obat;
use App\Models\Pasien;
use App\Models\PembelianObat;
use App\Models\PembelianObatDetail;
use App\Models\Pemeriksaan;
use App\Models\Resep;
use App\Models\ResepDetail;
use App\Models\StokObat;
use App\Models\StokObatMutasi;
use App\Models\User;
use App\Services\Obat\StokObatExpiryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class StokObatTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('2026-06-20 10:00:00');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_purchase_adds_separate_stock_rows_for_different_purchase_prices(): void
    {
        $obat = $this->createObat();
        $pembelian = PembelianObat::create(['tanggal_pembelian' => today()]);

        $this->createPurchaseDetail($pembelian, $obat, 1000, 10);
        $this->createPurchaseDetail($pembelian, $obat, 1200, 5);

        $this->assertDatabaseHas('stok_obats', [
            'obat_id' => $obat->id,
            'batch' => 'BATCH-001',
            'harga_beli' => 1000,
            'stok' => 10,
        ]);
        $this->assertDatabaseHas('stok_obats', [
            'obat_id' => $obat->id,
            'batch' => 'BATCH-001',
            'harga_beli' => 1200,
            'stok' => 5,
        ]);
    }

    public function test_consumed_purchase_detail_cannot_be_updated(): void
    {
        [$detail, $stok] = $this->purchaseWithDispensedStock();

        try {
            $detail->update(['jumlah' => 12]);
            $this->fail('Consumed purchase detail was updated.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('details', $exception->errors());
        }

        $this->assertSame(10, $detail->fresh()->jumlah);
        $this->assertSame(8, $stok->fresh()->stok);
    }

    public function test_consumed_purchase_detail_cannot_be_deleted(): void
    {
        [$detail, $stok] = $this->purchaseWithDispensedStock();

        try {
            $detail->delete();
            $this->fail('Consumed purchase detail was deleted.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('details', $exception->errors());
        }

        $this->assertNotNull($detail->fresh());
        $this->assertSame(8, $stok->fresh()->stok);
    }

    public function test_stock_expiring_today_is_available_and_cannot_be_removed_as_expired(): void
    {
        $obat = $this->createObat();
        $stok = StokObat::create([
            'obat_id' => $obat->id,
            'batch' => 'TODAY',
            'harga_beli' => 1000,
            'stok' => 5,
            'tgl_kadaluarsa' => today(),
        ]);

        $this->assertFalse($stok->isExpired());
        $this->assertSame(5, $obat->stokTersedia());

        $this->expectException(ValidationException::class);
        app(StokObatExpiryService::class)->removeExpired($stok);
    }

    public function test_expired_stock_can_be_removed_and_is_recorded_as_mutation(): void
    {
        $obat = $this->createObat();
        $stok = StokObat::create([
            'obat_id' => $obat->id,
            'batch' => 'EXPIRED',
            'harga_beli' => 1000,
            'stok' => 5,
            'tgl_kadaluarsa' => today()->subDay(),
        ]);

        app(StokObatExpiryService::class)->removeExpired($stok);

        $this->assertSame(0, $stok->fresh()->stok);
        $this->assertDatabaseHas('stok_obat_mutasis', [
            'stok_obat_id' => $stok->id,
            'tipe' => 'penghapusan_kadaluarsa',
            'jumlah_keluar' => 5,
        ]);
    }

    public function test_recipe_uses_nearest_expiry_stock_first(): void
    {
        $obat = $this->createObat();
        $nearest = StokObat::create([
            'obat_id' => $obat->id,
            'batch' => 'NEAR',
            'harga_beli' => 1000,
            'stok' => 5,
            'tgl_kadaluarsa' => today()->addMonth(),
        ]);
        $later = StokObat::create([
            'obat_id' => $obat->id,
            'batch' => 'LATER',
            'harga_beli' => 1100,
            'stok' => 10,
            'tgl_kadaluarsa' => today()->addMonths(2),
        ]);
        $resep = $this->createResep();

        ResepDetail::create([
            'resep_id' => $resep->id,
            'obat_id' => $obat->id,
            'jumlah' => 7,
            'aturan_pakai' => '2 x 1',
        ]);

        $this->assertSame(0, $nearest->fresh()->stok);
        $this->assertSame(8, $later->fresh()->stok);
        $this->assertDatabaseHas('stok_obat_mutasis', [
            'stok_obat_id' => $nearest->id,
            'tipe' => 'resep',
            'jumlah_keluar' => 5,
        ]);
        $this->assertDatabaseHas('stok_obat_mutasis', [
            'stok_obat_id' => $later->id,
            'tipe' => 'resep',
            'jumlah_keluar' => 2,
        ]);
    }

    public function test_stok_obat_query_handles_join_with_obats_without_ambiguous_stok_column_error(): void
    {
        $obat = $this->createObat();
        StokObat::create([
            'obat_id' => $obat->id,
            'batch' => 'BATCH-TEST',
            'harga_beli' => 1000,
            'stok' => 10,
            'tgl_kadaluarsa' => today()->addYear(),
        ]);

        $results = StokObat::query()
            ->where('stok_obats.stok', '>', 0)
            ->join('obats', 'stok_obats.obat_id', '=', 'obats.id')
            ->select('stok_obats.*')
            ->orderBy('obats.nama_obat')
            ->orderBy('stok_obats.tgl_kadaluarsa')
            ->get();

        $this->assertCount(1, $results);
    }

    private function purchaseWithDispensedStock(): array
    {
        $obat = $this->createObat();
        $pembelian = PembelianObat::create(['tanggal_pembelian' => today()]);
        $detail = $this->createPurchaseDetail($pembelian, $obat, 1000, 10);
        $stok = StokObat::where('obat_id', $obat->id)->firstOrFail();
        $stok->decrement('stok', 2);
        StokObatMutasi::create([
            'obat_id' => $obat->id,
            'stok_obat_id' => $stok->id,
            'tipe' => 'resep',
            'jumlah_keluar' => 2,
            'batch' => $stok->batch,
            'tgl_kadaluarsa' => $stok->tgl_kadaluarsa,
        ]);

        return [$detail, $stok];
    }

    private function createPurchaseDetail(
        PembelianObat $pembelian,
        Obat $obat,
        int $hargaBeli,
        int $jumlah
    ): PembelianObatDetail {
        return PembelianObatDetail::create([
            'pembelian_obat_id' => $pembelian->id,
            'obat_id' => $obat->id,
            'batch' => 'BATCH-001',
            'harga_beli' => $hargaBeli,
            'jumlah' => $jumlah,
            'tgl_kadaluarsa' => today()->addYear(),
        ]);
    }

    private function createObat(): Obat
    {
        return Obat::create([
            'nama_obat' => 'Paracetamol',
            'satuan' => 'Tablet',
            'harga_jual' => 2000,
        ]);
    }

    private function createResep(): Resep
    {
        $user = User::factory()->create(['role' => 'pasien']);
        $pasien = Pasien::create([
            'user_id' => $user->id,
            'nik' => '3201010101010001',
            'nama_pasien' => 'Budi Santoso',
            'tgl_lahir' => '1995-05-12',
            'jenis_kelamin' => 'Laki-laki',
            'alamat' => 'Jl. Sehat',
            'no_hp' => '081234567890',
        ]);
        $dokter = Dokter::create([
            'nama_dokter' => 'dr. Sehat',
            'spesialisasi' => 'Umum',
            'no_hp' => '081111111111',
        ]);
        $jadwal = JadwalDokter::create([
            'dokter_id' => $dokter->id,
            'hari' => 'Sabtu',
            'jam_mulai' => '08:00',
            'jam_selesai' => '12:00',
            'kuota' => 10,
        ]);
        $antrean = Antrean::create([
            'pasien_id' => $pasien->id,
            'dokter_id' => $dokter->id,
            'jadwal_dokter_id' => $jadwal->id,
            'tanggal_kunjungan' => today(),
            'nomor_antrean' => 1,
            'kode_antrean' => 'TEST-QUEUE-001',
            'status' => 'Selesai',
        ]);
        $pemeriksaan = Pemeriksaan::create([
            'antrean_id' => $antrean->id,
            'pasien_id' => $pasien->id,
            'dokter_id' => $dokter->id,
            'tgl_pemeriksaan' => today(),
            'keluhan' => 'Demam',
            'diagnosa' => 'Flu',
        ]);

        return Resep::create(['pemeriksaan_id' => $pemeriksaan->id]);
    }
}
