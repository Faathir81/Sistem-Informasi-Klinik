<?php

namespace App\Services\Resep;

use App\Models\Obat;
use App\Models\ResepDetail;
use Illuminate\Validation\ValidationException;

class ResepDetailStockService
{
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
        $this->adjustStock((int) $detail->obat_id, -((int) $detail->jumlah));
        $detail->resep?->recalculateTotal();
    }

    public function applyUpdating(ResepDetail $detail): void
    {
        $oldObatId = (int) $detail->getOriginal('obat_id');
        $oldJumlah = (int) $detail->getOriginal('jumlah');
        $newObatId = (int) $detail->obat_id;
        $newJumlah = (int) $detail->jumlah;

        if ($oldObatId === $newObatId) {
            $delta = $newJumlah - $oldJumlah;

            if ($delta > 0) {
                $this->ensureStock($newObatId, $delta);
            }

            $this->adjustStock($newObatId, -$delta);

            return;
        }

        $this->ensureStock($newObatId, $newJumlah);
        $this->adjustStock($oldObatId, $oldJumlah);
        $this->adjustStock($newObatId, -$newJumlah);
    }

    public function applyUpdated(ResepDetail $detail): void
    {
        $detail->resep?->recalculateTotal();
    }

    public function applyDeleted(ResepDetail $detail): void
    {
        $this->adjustStock((int) $detail->obat_id, (int) $detail->jumlah);
        $detail->resep?->recalculateTotal();
    }

    private function ensureStock(int $obatId, int $jumlah): void
    {
        $obat = Obat::query()
            ->whereKey($obatId)
            ->lockForUpdate()
            ->first();

        if (! $obat || $obat->stok < $jumlah) {
            throw ValidationException::withMessages([
                'obat_id' => 'Stok obat tidak mencukupi untuk jumlah resep yang diminta.',
            ]);
        }
    }

    private function adjustStock(int $obatId, int $delta): void
    {
        if ($delta === 0) {
            return;
        }

        $delta > 0
            ? Obat::whereKey($obatId)->increment('stok', $delta)
            : Obat::whereKey($obatId)->decrement('stok', abs($delta));
    }
}
