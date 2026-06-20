<?php

namespace App\Services\Obat;

use App\Models\PembelianObatDetail;
use App\Models\StokObat;
use App\Models\StokObatMutasi;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PembelianObatStockService
{
    public function __construct(
        private readonly ObatStockSummaryService $stockSummary,
    ) {}

    public function applyCreated(PembelianObatDetail $detail): void
    {
        DB::transaction(function () use ($detail): void {
            $stok = $this->stockRow($detail);
            $stok->increment('stok', (int) $detail->jumlah);

            $this->recordMutation($detail, $stok, (int) $detail->jumlah, 'pembelian');
            $detail->pembelianObat?->recalculateTotal();
            $this->stockSummary->sync((int) $detail->obat_id);
        });
    }

    public function applyUpdating(PembelianObatDetail $detail): void
    {
        DB::transaction(function () use ($detail): void {
            $oldObatId = (int) $detail->getOriginal('obat_id');
            $newObatId = (int) $detail->obat_id;

            $this->ensureOriginalStockCanBeReversed($detail);
            $this->reverseOriginal($detail);

            $stok = $this->stockRow($detail);
            $stok->increment('stok', (int) $detail->jumlah);

            $this->recordMutation($detail, $stok, (int) $detail->jumlah, 'koreksi_pembelian');
            $detail->pembelianObat?->recalculateTotal();
            $this->stockSummary->sync($oldObatId);
            $this->stockSummary->sync($newObatId);
        });
    }

    public function applyUpdated(PembelianObatDetail $detail): void
    {
        $detail->pembelianObat?->recalculateTotal();
    }

    public function applyDeleting(PembelianObatDetail $detail): void
    {
        DB::transaction(function () use ($detail): void {
            $this->ensureOriginalStockCanBeReversed($detail);
            $this->reverseOriginal($detail);
            $this->stockSummary->sync((int) $detail->obat_id);
        });
    }

    public function applyDeleted(PembelianObatDetail $detail): void
    {
        $detail->pembelianObat?->recalculateTotal();
    }

    private function reverseOriginal(PembelianObatDetail $detail): void
    {
        $stok = StokObat::query()
            ->where('obat_id', $detail->getOriginal('obat_id'))
            ->where('batch', $detail->getOriginal('batch'))
            ->where('harga_beli', $detail->getOriginal('harga_beli'))
            ->whereDate('tgl_kadaluarsa', $detail->getOriginal('tgl_kadaluarsa'))
            ->lockForUpdate()
            ->first();

        if (! $stok) {
            return;
        }

        $jumlah = (int) $detail->getOriginal('jumlah');
        $stok->decrement('stok', $jumlah);

        StokObatMutasi::create([
            'obat_id' => $stok->obat_id,
            'stok_obat_id' => $stok->id,
            'pembelian_obat_detail_id' => $detail->id,
            'tipe' => 'koreksi_pembelian',
            'jumlah_keluar' => $jumlah,
            'batch' => $stok->batch,
            'tgl_kadaluarsa' => $stok->tgl_kadaluarsa,
            'keterangan' => 'Koreksi pembelian obat',
        ]);
    }

    private function stockRow(PembelianObatDetail $detail): StokObat
    {
        return StokObat::query()->firstOrCreate(
            [
                'obat_id' => $detail->obat_id,
                'batch' => $detail->batch,
                'harga_beli' => $detail->harga_beli,
                'tgl_kadaluarsa' => $detail->tgl_kadaluarsa,
            ],
            [
                'stok' => 0,
            ],
        );
    }

    public function ensureOriginalStockCanBeReversed(PembelianObatDetail $detail): void
    {
        $stok = StokObat::query()
            ->where('obat_id', $detail->getOriginal('obat_id'))
            ->where('batch', $detail->getOriginal('batch'))
            ->where('harga_beli', $detail->getOriginal('harga_beli'))
            ->whereDate('tgl_kadaluarsa', $detail->getOriginal('tgl_kadaluarsa'))
            ->lockForUpdate()
            ->first();

        $hasDispensing = $stok?->mutasis()
            ->where('tipe', 'resep')
            ->where('created_at', '>=', $detail->created_at)
            ->exists() ?? false;

        if ($hasDispensing || ! $stok || $stok->stok < (int) $detail->getOriginal('jumlah')) {
            throw ValidationException::withMessages([
                'details' => 'Pembelian tidak dapat diubah atau dihapus karena stok batch ini sudah dipakai pada resep.',
            ]);
        }
    }

    private function recordMutation(PembelianObatDetail $detail, StokObat $stok, int $jumlah, string $tipe): void
    {
        StokObatMutasi::create([
            'obat_id' => $detail->obat_id,
            'stok_obat_id' => $stok->id,
            'pembelian_obat_detail_id' => $detail->id,
            'tipe' => $tipe,
            'jumlah_masuk' => $jumlah,
            'batch' => $stok->batch,
            'tgl_kadaluarsa' => $stok->tgl_kadaluarsa,
            'keterangan' => 'Pembelian obat',
        ]);
    }
}
