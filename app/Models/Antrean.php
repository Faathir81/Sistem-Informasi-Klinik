<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Antrean extends Model
{
    protected $fillable = [
        'pasien_id',
        'dokter_id',
        'jadwal_dokter_id',
        'tanggal_kunjungan',
        'nomor_antrean',
        'kode_antrean',
        'status',
    ];

    protected $casts = [
        'tanggal_kunjungan' => 'date',
    ];

    public function pasien(): BelongsTo
    {
        return $this->belongsTo(Pasien::class);
    }

    public function dokter(): BelongsTo
    {
        return $this->belongsTo(Dokter::class);
    }

    public function jadwalDokter(): BelongsTo
    {
        return $this->belongsTo(JadwalDokter::class);
    }

    public function pemeriksaan(): HasOne
    {
        return $this->hasOne(Pemeriksaan::class);
    }
}
