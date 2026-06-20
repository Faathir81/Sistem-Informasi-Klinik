<?php

namespace App\Services\Obat;

use App\Models\StokObat;
use App\Models\StokObatMutasi;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StokObatExpiryService
{
    public function __construct(
        private readonly ObatStockSummaryService $stockSummary,
    ) {}

    public function removeExpired(StokObat $stokObat): void
    {
        DB::transaction(function () use ($stokObat): void {
            $stokObat = StokObat::query()
                ->lockForUpdate()
                ->findOrFail($stokObat->getKey());

            if ($stokObat->stok <= 0) {
                return;
            }

            if (! $stokObat->isExpired()) {
                throw ValidationException::withMessages([
                    'tgl_kadaluarsa' => 'Obat belum melewati tanggal kedaluwarsa dan tidak dapat dihapus.',
                ]);
            }

            $jumlah = (int) $stokObat->stok;

            $stokObat->update(['stok' => 0]);

            StokObatMutasi::create([
                'obat_id' => $stokObat->obat_id,
                'stok_obat_id' => $stokObat->id,
                'tipe' => 'penghapusan_kadaluarsa',
                'jumlah_keluar' => $jumlah,
                'batch' => $stokObat->batch,
                'tgl_kadaluarsa' => $stokObat->tgl_kadaluarsa,
                'keterangan' => 'Penghapusan obat kadaluwarsa',
            ]);

            $this->stockSummary->sync((int) $stokObat->obat_id);
        });
    }
}
