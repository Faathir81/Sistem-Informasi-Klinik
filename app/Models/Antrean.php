<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

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

    /**
     * Auto-generate nomor antrean urut & kode unik QR saat antrean baru dibuat.
     */
    protected static function booted(): void
    {
        static::creating(function (Antrean $antrean) {
            // Hitung nomor urut: ambil antrean terakhir untuk dokter + tanggal yang sama
            $lastNomor = self::where('dokter_id', $antrean->dokter_id)
                ->where('tanggal_kunjungan', $antrean->tanggal_kunjungan)
                ->whereNotIn('status', ['Batal'])
                ->max('nomor_antrean') ?? 0;

            $antrean->nomor_antrean = $lastNomor + 1;

            // Generate kode unik untuk QR Code: YYYYMMDD-XXXX (e.g., 20260524-A1B2)
            $antrean->kode_antrean = strtoupper(
                now()->format('Ymd') . '-' . Str::random(6)
            );
        });
    }

    // Relasi
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
}
