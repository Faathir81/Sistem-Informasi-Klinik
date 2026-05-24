<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaksi extends Model
{
    protected $fillable = [
        'pemeriksaan_id',
        'order_id',
        'amount',
        'status',
        'snap_token',
        'snap_url',
        'payment_type',
        'tgl_bayar',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'tgl_bayar' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saved(function (Transaksi $transaksi) {
            if ($transaksi->status === 'SETTLEMENT') {
                $transaksi->pemeriksaan()->update([
                    'status_bayar' => 'Lunas',
                ]);
            }
        });
    }

    public function pemeriksaan(): BelongsTo
    {
        return $this->belongsTo(Pemeriksaan::class);
    }

    public function markSettled(?string $paymentType = null): void
    {
        $this->update([
            'status' => 'SETTLEMENT',
            'payment_type' => $paymentType ?? $this->payment_type,
            'tgl_bayar' => now(),
        ]);

        $this->pemeriksaan()->update([
            'status_bayar' => 'Lunas',
        ]);
    }
}
