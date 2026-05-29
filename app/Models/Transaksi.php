<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Enums\TransaksiStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaksi extends Model
{
    protected $fillable = [
        'pemeriksaan_id',
        'pengajuan_pasien_id',
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
            if ($transaksi->status === TransaksiStatus::Settlement->value) {
                $transaksi->pemeriksaan?->update([
                    'status_bayar' => PaymentStatus::Lunas->value,
                ]);

                $transaksi->pengajuanPasien?->approveFromPayment();
            }
        });
    }

    public function pemeriksaan(): BelongsTo
    {
        return $this->belongsTo(Pemeriksaan::class);
    }

    public function pengajuanPasien(): BelongsTo
    {
        return $this->belongsTo(PengajuanPasien::class);
    }

    public function markSettled(?string $paymentType = null): void
    {
        $this->update([
            'status' => TransaksiStatus::Settlement->value,
            'payment_type' => $paymentType ?? $this->payment_type,
            'tgl_bayar' => now(),
        ]);

        $this->pemeriksaan?->update([
            'status_bayar' => PaymentStatus::Lunas->value,
        ]);

        $this->pengajuanPasien?->approveFromPayment();
    }
}
