<?php

namespace App\Models;

use App\Services\Resep\ResepDetailStockService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
            app(ResepDetailStockService::class)->prepareForSave($detail);
        });

        static::creating(function (ResepDetail $detail) {
            app(ResepDetailStockService::class)->reserveForCreate($detail);
        });

        static::created(function (ResepDetail $detail) {
            app(ResepDetailStockService::class)->applyCreated($detail);
        });

        static::updating(function (ResepDetail $detail) {
            app(ResepDetailStockService::class)->applyUpdating($detail);
        });

        static::updated(function (ResepDetail $detail) {
            app(ResepDetailStockService::class)->applyUpdated($detail);
        });

        static::deleting(function (ResepDetail $detail) {
            app(ResepDetailStockService::class)->applyDeleting($detail);
        });

        static::deleted(function (ResepDetail $detail) {
            app(ResepDetailStockService::class)->applyDeleted($detail);
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
}
