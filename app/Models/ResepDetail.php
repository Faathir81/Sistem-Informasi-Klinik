<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class ResepDetail extends Model
{
    protected $fillable = [
        'resep_id',
        'obat_id',
        'jumlah',
        'aturan_pakai',
        'sub_total',
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'sub_total' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (ResepDetail $detail) {
            $obat = Obat::find($detail->obat_id);

            if (! $obat) {
                return;
            }

            $detail->sub_total = $obat->harga_jual * $detail->jumlah;
        });

        static::creating(function (ResepDetail $detail) {
            static::ensureStock($detail->obat_id, $detail->jumlah);
        });

        static::created(function (ResepDetail $detail) {
            static::adjustStock($detail->obat_id, -$detail->jumlah);
            $detail->resep?->recalculateTotal();
        });

        static::updating(function (ResepDetail $detail) {
            $oldObatId = (int) $detail->getOriginal('obat_id');
            $oldJumlah = (int) $detail->getOriginal('jumlah');
            $newObatId = (int) $detail->obat_id;
            $newJumlah = (int) $detail->jumlah;

            if ($oldObatId === $newObatId) {
                $delta = $newJumlah - $oldJumlah;

                if ($delta > 0) {
                    static::ensureStock($newObatId, $delta);
                }

                static::adjustStock($newObatId, -$delta);

                return;
            }

            static::ensureStock($newObatId, $newJumlah);
            static::adjustStock($oldObatId, $oldJumlah);
            static::adjustStock($newObatId, -$newJumlah);
        });

        static::updated(function (ResepDetail $detail) {
            $detail->resep?->recalculateTotal();
        });

        static::deleted(function (ResepDetail $detail) {
            static::adjustStock($detail->obat_id, $detail->jumlah);
            $detail->resep?->recalculateTotal();
        });
    }

    public function resep(): BelongsTo
    {
        return $this->belongsTo(Resep::class);
    }

    public function obat(): BelongsTo
    {
        return $this->belongsTo(Obat::class);
    }

    private static function ensureStock(int $obatId, int $jumlah): void
    {
        $obat = Obat::find($obatId);

        if (! $obat || $obat->stok < $jumlah) {
            throw ValidationException::withMessages([
                'obat_id' => 'Stok obat tidak mencukupi untuk jumlah resep yang diminta.',
            ]);
        }
    }

    private static function adjustStock(int $obatId, int $delta): void
    {
        if ($delta === 0) {
            return;
        }

        Obat::whereKey($obatId)->increment('stok', $delta);
    }
}
