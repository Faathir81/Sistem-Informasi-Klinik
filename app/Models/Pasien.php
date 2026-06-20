<?php

namespace App\Models;

use App\Services\Pasien\MedicalRecordNumberService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pasien extends Model
{
    protected $fillable = [
        'user_id',
        'nik',
        'nama_pasien',
        'tgl_lahir',
        'jenis_kelamin',
        'alamat',
        'no_hp',
    ];

    protected $hidden = [
        'no_rekam_medis',
    ];

    protected $casts = [
        'tgl_lahir' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (Pasien $pasien) {
            if (! $pasien->no_rekam_medis) {
                $pasien->no_rekam_medis = app(MedicalRecordNumberService::class)->next();
            }
        });
    }

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

    public function pengajuanPasien(): HasOne
    {
        return $this->hasOne(PengajuanPasien::class);
    }
}
