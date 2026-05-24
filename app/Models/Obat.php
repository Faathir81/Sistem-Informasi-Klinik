<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Obat extends Model
{
    protected $fillable = [
        'nama_obat',
        'satuan',
        'stok',
        'harga_beli',
        'harga_jual',
        'tgl_kadaluarsa',
    ];

    protected $casts = [
        'stok' => 'integer',
        'harga_beli' => 'decimal:2',
        'harga_jual' => 'decimal:2',
        'tgl_kadaluarsa' => 'date',
    ];

    public function resepDetails(): HasMany
    {
        return $this->hasMany(ResepDetail::class);
    }

    public function scopeStokKritis(Builder $query): Builder
    {
        return $query->where('stok', '<', 10);
    }

    public function scopeKadaluarsaSegera(Builder $query): Builder
    {
        return $query
            ->whereNotNull('tgl_kadaluarsa')
            ->whereDate('tgl_kadaluarsa', '<=', now()->addDays(30));
    }
}
