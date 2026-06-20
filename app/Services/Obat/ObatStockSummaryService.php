<?php

namespace App\Services\Obat;

use App\Models\Obat;
use App\Models\StokObat;

class ObatStockSummaryService
{
    public function sync(int $obatId): void
    {
        $nearestStock = StokObat::query()
            ->where('obat_id', $obatId)
            ->where('stok', '>', 0)
            ->orderBy('tgl_kadaluarsa')
            ->first();

        Obat::whereKey($obatId)->update([
            'stok' => StokObat::where('obat_id', $obatId)->sum('stok'),
            'harga_beli' => $nearestStock?->harga_beli ?? 0,
            'tgl_kadaluarsa' => $nearestStock?->tgl_kadaluarsa,
        ]);
    }
}
