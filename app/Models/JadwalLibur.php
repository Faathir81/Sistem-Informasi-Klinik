<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalLibur extends Model
{
    protected $fillable = [
        'dokter_id',
        'tanggal',
        'keterangan',
        'status_aktif',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'status_aktif' => 'boolean',
    ];

    public function dokter(): BelongsTo
    {
        return $this->belongsTo(Dokter::class);
    }
}
