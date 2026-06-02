<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Layanan extends Model
{
    protected $table = 'layanans';

    protected $fillable = [
        'nama_layanan',
        'tarif_default',
        'status_aktif',
    ];

    protected $casts = [
        'tarif_default' => 'decimal:2',
        'status_aktif' => 'boolean',
    ];

    public function pemeriksaanTindakans(): HasMany
    {
        return $this->hasMany(PemeriksaanTindakan::class);
    }
}
