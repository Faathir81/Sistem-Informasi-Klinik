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
        'harga_jual',
        'stok',
        'harga_beli',
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

    public function stokObats(): HasMany
    {
        return $this->hasMany(StokObat::class);
    }

    public function mutasis(): HasMany
    {
        return $this->hasMany(StokObatMutasi::class);
    }

    public function scopeStokKritis(Builder $query): Builder
    {
        return $query->whereRaw('(select coalesce(sum(stok), 0) from stok_obats where stok_obats.obat_id = obats.id) < 10');
    }

    public function scopeKadaluarsaSegera(Builder $query): Builder
    {
        return $query->whereHas('stokObats', fn (Builder $stok): Builder => $stok
            ->where('stok_obats.stok', '>', 0)
            ->whereDate('stok_obats.tgl_kadaluarsa', '<=', now()->addDays(30)));
    }

    public function totalStok(): int
    {
        if ($this->relationLoaded('stokObats')) {
            return (int) $this->stokObats->sum('stok');
        }

        return (int) $this->stokObats()->sum('stok');
    }

    public function stokTersedia(): int
    {
        return (int) $this->stokObats()
            ->where('stok_obats.stok', '>', 0)
            ->whereDate('stok_obats.tgl_kadaluarsa', '>=', now()->toDateString())
            ->sum('stok');
    }
}
