<?php

namespace App\Models;

use App\Enums\PayrollPaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gaji extends Model
{
    protected $fillable = [
        'role',
        'dokter_id',
        'pegawai_id',
        'bulan_tahun',
        'gaji_pokok',
        'tunjangan',
        'potongan',
        'total_diterima',
        'status_bayar',
        'tgl_bayar',
    ];

    protected $casts = [
        'gaji_pokok' => 'decimal:2',
        'tunjangan' => 'decimal:2',
        'potongan' => 'decimal:2',
        'total_diterima' => 'decimal:2',
        'tgl_bayar' => 'date',
    ];

    protected static function booted(): void
    {
        static::saving(function (Gaji $gaji) {
            if ($gaji->role === 'Dokter') {
                $gaji->pegawai_id = null;
            }

            if ($gaji->role === 'Pegawai') {
                $gaji->dokter_id = null;
            }

            $gaji->total_diterima = max(
                0,
                (float) $gaji->gaji_pokok + (float) $gaji->tunjangan - (float) $gaji->potongan
            );

            if ($gaji->status_bayar === PayrollPaymentStatus::Lunas->value && ! $gaji->tgl_bayar) {
                $gaji->tgl_bayar = now()->toDateString();
            }
        });
    }

    public function dokter(): BelongsTo
    {
        return $this->belongsTo(Dokter::class);
    }

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function namaPenerima(): string
    {
        return $this->role === 'Dokter'
            ? ($this->dokter?->nama_dokter ?? '-')
            : ($this->pegawai?->nama_pegawai ?? '-');
    }
}
