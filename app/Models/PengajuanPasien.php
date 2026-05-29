<?php

namespace App\Models;

use App\Enums\PengajuanPasienStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

class PengajuanPasien extends Model
{
    protected $fillable = [
        'user_id',
        'pasien_id',
        'nik',
        'nama_pasien',
        'tgl_lahir',
        'jenis_kelamin',
        'alamat',
        'no_hp',
        'catatan_pasien',
        'status',
        'reviewed_at',
    ];

    protected $casts = [
        'tgl_lahir' => 'date',
        'reviewed_at' => 'datetime',
    ];

    public function approveFromPayment(): Pasien
    {
        return DB::transaction(function () {
            $this->refresh();

            if ($this->status === PengajuanPasienStatus::Disetujui->value && $this->pasien) {
                return $this->pasien;
            }

            if (! in_array($this->status, [
                PengajuanPasienStatus::MenungguPembayaran->value,
                PengajuanPasienStatus::PembayaranGagal->value,
                PengajuanPasienStatus::Menunggu->value,
            ], true)) {
                throw new \RuntimeException('Pengajuan ini tidak dapat diproses otomatis.');
            }

            if ($this->user->pasien) {
                $this->update([
                    'status' => PengajuanPasienStatus::Disetujui->value,
                    'pasien_id' => $this->user->pasien->id,
                    'reviewed_at' => now(),
                ]);

                return $this->user->pasien;
            }

            if (Pasien::where('nik', $this->nik)->exists()) {
                throw new \RuntimeException('NIK ini sudah terdaftar sebagai pasien.');
            }

            $pasien = Pasien::create([
                'user_id' => $this->user_id,
                'nik' => $this->nik,
                'nama_pasien' => $this->nama_pasien,
                'tgl_lahir' => $this->tgl_lahir,
                'jenis_kelamin' => $this->jenis_kelamin,
                'alamat' => $this->alamat,
                'no_hp' => $this->no_hp,
            ]);

            $this->update([
                'status' => PengajuanPasienStatus::Disetujui->value,
                'pasien_id' => $pasien->id,
                'reviewed_at' => now(),
            ]);

            return $pasien;
        });
    }

    public function markPaymentFailed(): void
    {
        $this->update(['status' => PengajuanPasienStatus::PembayaranGagal->value]);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pasien(): BelongsTo
    {
        return $this->belongsTo(Pasien::class);
    }

    public function transaksi(): HasOne
    {
        return $this->hasOne(Transaksi::class);
    }
}
