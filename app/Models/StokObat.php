<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class StokObat extends Model
{
    protected $fillable = [
        'obat_id',
        'batch',
        'harga_beli',
        'stok',
        'tgl_kadaluarsa',
    ];

    protected $casts = [
        'harga_beli' => 'decimal:2',
        'stok' => 'integer',
        'tgl_kadaluarsa' => 'date',
    ];

    public function obat(): BelongsTo
    {
        return $this->belongsTo(Obat::class);
    }

    public function mutasis(): HasMany
    {
        return $this->hasMany(StokObatMutasi::class);
    }

    public function scopeTersedia(Builder $query): Builder
    {
        return $query
            ->where('stok_obats.stok', '>', 0)
            ->whereDate('stok_obats.tgl_kadaluarsa', '>=', now()->toDateString());
    }

    public function scopeKadaluarsa(Builder $query): Builder
    {
        return $query
            ->where('stok_obats.stok', '>', 0)
            ->whereDate('stok_obats.tgl_kadaluarsa', '<', now()->toDateString());
    }

    public function isExpired(?Carbon $referenceDate = null): bool
    {
        $referenceDate ??= now();

        return $this->tgl_kadaluarsa->startOfDay()->lt($referenceDate->startOfDay());
    }
}
