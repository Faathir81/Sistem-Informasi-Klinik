<?php

namespace App\Services\Resep;

use App\Models\Obat;
use App\Models\ResepDetail;
use App\Models\StokObat;
use App\Models\StokObatMutasi;
use App\Services\Obat\ObatStockSummaryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ResepDetailStockService
{
    public function __construct(
        private readonly ObatStockSummaryService $stockSummary,
    ) {}

    public function prepareForSave(ResepDetail $detail): void
    {
        $obat = Obat::find($detail->obat_id);

        if ($obat) {
            $detail->sub_total = $obat->harga_jual * $detail->jumlah;
        }
    }

    public function reserveForCreate(ResepDetail $detail): void
    {
        $this->ensureStock((int) $detail->obat_id, (int) $detail->jumlah);
    }

    public function applyCreated(ResepDetail $detail): void
    {
        DB::transaction(function () use ($detail): void {
            $this->takeStockFefo($detail, (int) $detail->obat_id, (int) $detail->jumlah);
            $detail->resep?->recalculateTotal();
            $this->stockSummary->sync((int) $detail->obat_id);
        });
    }

    public function applyUpdating(ResepDetail $detail): void
    {
        DB::transaction(function () use ($detail): void {
            $oldObatId = (int) $detail->getOriginal('obat_id');
            $newObatId = (int) $detail->obat_id;
            $newJumlah = (int) $detail->jumlah;

            if ($oldObatId === $newObatId) {
                $this->returnRecipeStock($detail);
                $this->takeStockFefo($detail, $newObatId, $newJumlah);
                $this->stockSummary->sync($newObatId);

                return;
            }

            $this->ensureStock($newObatId, $newJumlah);
            $this->returnRecipeStock($detail);
            $this->takeStockFefo($detail, $newObatId, $newJumlah);
            $this->stockSummary->sync($oldObatId);
            $this->stockSummary->sync($newObatId);
        });
    }

    public function applyUpdated(ResepDetail $detail): void
    {
        $detail->resep?->recalculateTotal();
    }

    public function applyDeleting(ResepDetail $detail): void
    {
        DB::transaction(function () use ($detail): void {
            $this->returnRecipeStock($detail);
            $this->stockSummary->sync((int) $detail->obat_id);
        });
    }

    public function applyDeleted(ResepDetail $detail): void
    {
        $detail->resep?->recalculateTotal();
    }

    private function ensureStock(int $obatId, int $jumlah): void
    {
        $tersedia = StokObat::query()
            ->where('obat_id', $obatId)
            ->tersedia()
            ->lockForUpdate()
            ->sum('stok');

        if ($tersedia < $jumlah) {
            throw ValidationException::withMessages([
                'obat_id' => 'Stok obat tidak mencukupi untuk jumlah resep yang diminta.',
            ]);
        }
    }

    private function takeStockFefo(ResepDetail $detail, int $obatId, int $jumlah): void
    {
        $sisa = $jumlah;
        $stocks = StokObat::query()
            ->where('obat_id', $obatId)
            ->tersedia()
            ->orderBy('tgl_kadaluarsa')
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        foreach ($stocks as $stock) {
            if ($sisa <= 0) {
                break;
            }

            $keluar = min((int) $stock->stok, $sisa);
            $stock->decrement('stok', $keluar);
            $sisa -= $keluar;

            StokObatMutasi::create([
                'obat_id' => $obatId,
                'stok_obat_id' => $stock->id,
                'resep_detail_id' => $detail->id,
                'tipe' => 'resep',
                'jumlah_keluar' => $keluar,
                'batch' => $stock->batch,
                'tgl_kadaluarsa' => $stock->tgl_kadaluarsa,
                'keterangan' => 'Pengeluaran resep obat',
            ]);
        }

        if ($sisa > 0) {
            throw ValidationException::withMessages([
                'obat_id' => 'Stok obat tidak mencukupi untuk jumlah resep yang diminta.',
            ]);
        }
    }

    private function returnRecipeStock(ResepDetail $detail): void
    {
        $mutasis = StokObatMutasi::query()
            ->where('resep_detail_id', $detail->id)
            ->where('tipe', 'resep')
            ->where('jumlah_keluar', '>', 0)
            ->get();

        foreach ($mutasis as $mutasi) {
            if (! $mutasi->stokObat) {
                continue;
            }

            $mutasi->stokObat->increment('stok', (int) $mutasi->jumlah_keluar);

            StokObatMutasi::create([
                'obat_id' => $mutasi->obat_id,
                'stok_obat_id' => $mutasi->stok_obat_id,
                'resep_detail_id' => $detail->id,
                'tipe' => 'koreksi_resep',
                'jumlah_masuk' => $mutasi->jumlah_keluar,
                'batch' => $mutasi->batch,
                'tgl_kadaluarsa' => $mutasi->tgl_kadaluarsa,
                'keterangan' => 'Pengembalian stok karena resep diubah/dihapus',
            ]);
        }

        StokObatMutasi::query()
            ->where('resep_detail_id', $detail->id)
            ->where('tipe', 'resep')
            ->delete();
    }
}
