<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class PengajuanPasien extends Model
{
    protected $fillable = [
        'user_id',
        'pasien_id',
        'reviewed_by',
        'nik',
        'nama_pasien',
        'tgl_lahir',
        'jenis_kelamin',
        'alamat',
        'no_hp',
        'catatan_pasien',
        'status',
        'alasan_penolakan',
        'reviewed_at',
    ];

    protected $casts = [
        'tgl_lahir' => 'date',
        'reviewed_at' => 'datetime',
    ];

    public function approve(User $admin): Pasien
    {
        return DB::transaction(function () use ($admin) {
            if ($this->status !== 'Menunggu') {
                throw new \RuntimeException('Pengajuan ini sudah diverifikasi.');
            }

            if ($this->user->pasien) {
                throw new \RuntimeException('Akun pasien ini sudah memiliki nomor rekam medis.');
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
                'status' => 'Disetujui',
                'pasien_id' => $pasien->id,
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
                'alasan_penolakan' => null,
            ]);

            return $pasien;
        });
    }

    public function reject(User $admin, string $reason): void
    {
        if ($this->status !== 'Menunggu') {
            throw new \RuntimeException('Pengajuan ini sudah diverifikasi.');
        }

        $this->update([
            'status' => 'Ditolak',
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
            'alasan_penolakan' => $reason,
        ]);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pasien(): BelongsTo
    {
        return $this->belongsTo(Pasien::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
