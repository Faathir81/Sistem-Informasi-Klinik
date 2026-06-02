<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PemeriksaanTindakan extends Model
{
    protected $fillable = [
        'pemeriksaan_id',
        'layanan_id',
        'nama_layanan',
        'tarif',
        'catatan',
    ];

    protected $casts = [
        'tarif' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (PemeriksaanTindakan $tindakan): void {
            if (! $tindakan->layanan_id) {
                return;
            }

            $layanan = Layanan::find($tindakan->layanan_id);

            if (! $layanan) {
                return;
            }

            $tindakan->nama_layanan = $tindakan->nama_layanan ?: $layanan->nama_layanan;

            if ((float) $tindakan->tarif <= 0) {
                $tindakan->tarif = $layanan->tarif_default;
            }
        });
    }

    public function pemeriksaan(): BelongsTo
    {
        return $this->belongsTo(Pemeriksaan::class);
    }

    public function layanan(): BelongsTo
    {
        return $this->belongsTo(Layanan::class);
    }
}
