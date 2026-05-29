<?php

namespace App\Services\Antrean;

use App\Enums\AntreanStatus;
use App\Models\Antrean;

class LiveQueuePreviewService
{
    public function current(): ?Antrean
    {
        return Antrean::query()
            ->with(['dokter', 'jadwalDokter'])
            ->whereDate('tanggal_kunjungan', today())
            ->whereIn('status', AntreanStatus::activeValues())
            ->orderByRaw(
                'CASE WHEN status = ? THEN 0 WHEN status = ? THEN 1 ELSE 2 END',
                [AntreanStatus::Dipanggil->value, AntreanStatus::Menunggu->value],
            )
            ->orderBy('nomor_antrean')
            ->first();
    }

    public function payload(): array
    {
        $antrean = $this->current();

        if (! $antrean) {
            return [
                'active' => false,
                'number' => '--',
                'status' => 'Belum Ada',
                'doctor' => 'Belum ada antrean aktif',
                'schedule' => 'Booking antrean untuk hari ini',
                'code' => 'Belum tersedia',
                'updated_at' => now()->format('H:i:s'),
            ];
        }

        return [
            'active' => true,
            'number' => str_pad((string) $antrean->nomor_antrean, 3, '0', STR_PAD_LEFT),
            'status' => $antrean->status,
            'doctor' => $antrean->dokter?->nama_dokter ?? 'Dokter belum tersedia',
            'schedule' => $antrean->jadwalDokter
                ? substr($antrean->jadwalDokter->jam_mulai, 0, 5).' - '.substr($antrean->jadwalDokter->jam_selesai, 0, 5).' WIB'
                : 'Jadwal belum tersedia',
            'code' => $this->maskQueueCode($antrean->kode_antrean),
            'updated_at' => now()->format('H:i:s'),
        ];
    }

    public function maskQueueCode(?string $code): string
    {
        if (! $code) {
            return 'Belum tersedia';
        }

        return substr($code, 0, 9).'****'.substr($code, -2);
    }
}
