<?php

namespace App\Models;

use App\Services\Obat\PembelianObatStockService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PembelianObat extends Model
{
    protected $fillable = [
        'tanggal_pembelian',
        'supplier',
        'total_pembelian',
        'catatan',
    ];

    protected $casts = [
        'tanggal_pembelian' => 'date',
        'total_pembelian' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::deleting(function (PembelianObat $pembelian): void {
            $pembelian->details->each(
                fn (PembelianObatDetail $detail) => app(PembelianObatStockService::class)
                    ->ensureOriginalStockCanBeReversed($detail)
            );
            $pembelian->details->each->delete();
        });
    }

    public function details(): HasMany
    {
        return $this->hasMany(PembelianObatDetail::class);
    }

    public function recalculateTotal(): void
    {
        $this->updateQuietly([
            'total_pembelian' => $this->details()->sum('sub_total'),
        ]);
    }
}
