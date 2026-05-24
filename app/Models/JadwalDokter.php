<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalDokter extends Model
{
    protected $fillable = [
        'dokter_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'kuota',
    ];

    // Relasi
    public function dokter(): BelongsTo
    {
        return $this->belongsTo(Dokter::class);
    }
}
