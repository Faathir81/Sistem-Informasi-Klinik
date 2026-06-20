<?php

namespace App\Models;

use App\Services\Obat\PembelianObatStockService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembelianObatDetail extends Model
{
    protected $fillable = [
        'pembelian_obat_id',
        'obat_id',
        'batch',
        'harga_beli',
        'jumlah',
        'tgl_kadaluarsa',
        'sub_total',
    ];

    protected $casts = [
        'harga_beli' => 'decimal:2',
        'jumlah' => 'integer',
        'tgl_kadaluarsa' => 'date',
        'sub_total' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (PembelianObatDetail $detail): void {
            $detail->sub_total = (float) $detail->harga_beli * (int) $detail->jumlah;
        });

        static::created(function (PembelianObatDetail $detail): void {
            app(PembelianObatStockService::class)->applyCreated($detail);
        });

        static::updating(function (PembelianObatDetail $detail): void {
            app(PembelianObatStockService::class)->applyUpdating($detail);
        });

        static::updated(function (PembelianObatDetail $detail): void {
            app(PembelianObatStockService::class)->applyUpdated($detail);
        });

        static::deleting(function (PembelianObatDetail $detail): void {
            app(PembelianObatStockService::class)->applyDeleting($detail);
        });

        static::deleted(function (PembelianObatDetail $detail): void {
            app(PembelianObatStockService::class)->applyDeleted($detail);
        });
    }

    public function pembelianObat(): BelongsTo
    {
        return $this->belongsTo(PembelianObat::class);
    }

    public function obat(): BelongsTo
    {
        return $this->belongsTo(Obat::class);
    }
}
