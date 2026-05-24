<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resep extends Model
{
    protected $fillable = [
        'pemeriksaan_id',
        'total_harga_obat',
        'status_ambil',
    ];

    protected $casts = [
        'total_harga_obat' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::deleting(function (Resep $resep) {
            $resep->details->each->delete();
        });
    }

    public function pemeriksaan(): BelongsTo
    {
        return $this->belongsTo(Pemeriksaan::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(ResepDetail::class);
    }

    public function recalculateTotal(): void
    {
        $this->updateQuietly([
            'total_harga_obat' => $this->details()->sum('sub_total'),
        ]);
    }
}
