<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pemeriksaan extends Model
{
    protected $fillable = [
        'antrean_id',
        'pasien_id',
        'dokter_id',
        'tgl_pemeriksaan',
        'keluhan',
        'diagnosa',
        'tindakan',
        'biaya_konsultasi',
        'status_bayar',
    ];

    protected $casts = [
        'tgl_pemeriksaan' => 'date',
        'biaya_konsultasi' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::deleting(function (Pemeriksaan $pemeriksaan) {
            $pemeriksaan->resep?->delete();
        });
    }

    public function antrean(): BelongsTo
    {
        return $this->belongsTo(Antrean::class);
    }

    public function pasien(): BelongsTo
    {
        return $this->belongsTo(Pasien::class);
    }

    public function dokter(): BelongsTo
    {
        return $this->belongsTo(Dokter::class);
    }

    public function resep(): HasOne
    {
        return $this->hasOne(Resep::class);
    }
}
