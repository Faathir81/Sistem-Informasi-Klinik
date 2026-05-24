<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Pasien extends Model
{
    protected $fillable = [
        'user_id',
        'no_rekam_medis',
        'nik',
        'nama_pasien',
        'tgl_lahir',
        'jenis_kelamin',
        'alamat',
        'no_hp',
    ];

    protected $casts = [
        'tgl_lahir' => 'date',
    ];

    /**
     * Auto-generate Nomor Rekam Medis (RM-YYYYMMDD-XXXX) saat pasien baru dibuat.
     */
    protected static function booted(): void
    {
        static::creating(function (Pasien $pasien) {
            $prefix = 'RM-'.Carbon::now()->format('Ymd').'-';
            $last = self::where('no_rekam_medis', 'like', $prefix.'%')
                ->orderByDesc('no_rekam_medis')
                ->first();

            if ($last) {
                $lastNumber = (int) substr($last->no_rekam_medis, -4);
                $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $nextNumber = '0001';
            }

            $pasien->no_rekam_medis = $prefix.$nextNumber;
        });
    }

    // Relasi
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function antreans(): HasMany
    {
        return $this->hasMany(Antrean::class);
    }

    public function pemeriksaans(): HasMany
    {
        return $this->hasMany(Pemeriksaan::class);
    }
}
