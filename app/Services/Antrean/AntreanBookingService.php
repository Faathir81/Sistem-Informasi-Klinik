<?php

namespace App\Services\Antrean;

use App\Enums\AntreanStatus;
use App\Models\Antrean;
use App\Models\Dokter;
use App\Models\JadwalDokter;
use App\Models\Pasien;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AntreanBookingService
{
    public function availableSchedules(int $dokterId, string $tanggal)
    {
        $tanggalKunjungan = Carbon::parse($tanggal, $this->timezone())->startOfDay();
        $hari = $tanggalKunjungan->locale('id')->isoFormat('dddd');

        return JadwalDokter::with('dokter')
            ->where('dokter_id', $dokterId)
            ->where('hari', $hari)
            ->orderBy('jam_mulai')
            ->get()
            ->filter(fn (JadwalDokter $jadwal): bool => $this->isScheduleBookable($jadwal, $tanggalKunjungan))
            ->map(function (JadwalDokter $jadwal) use ($tanggal): JadwalDokter {
                $terpakai = Antrean::query()
                    ->where('jadwal_dokter_id', $jadwal->id)
                    ->where('tanggal_kunjungan', $tanggal)
                    ->where('status', '!=', AntreanStatus::Batal->value)
                    ->count();

                $jadwal->sisa_kuota = $jadwal->kuota - $terpakai;

                return $jadwal;
            })
            ->values();
    }

    public function create(Pasien $pasien, array $data): Antrean
    {
        return DB::transaction(function () use ($pasien, $data): Antrean {
            Dokter::query()
                ->whereKey($data['dokter_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $jadwal = JadwalDokter::query()
                ->whereKey($data['jadwal_dokter_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if ((int) $jadwal->dokter_id !== (int) $data['dokter_id']) {
                throw ValidationException::withMessages([
                    'jadwal_dokter_id' => 'Jadwal tidak sesuai dengan dokter yang dipilih.',
                ]);
            }

            $tanggalKunjungan = Carbon::parse($data['tanggal_kunjungan'], $this->timezone())->startOfDay();

            $this->ensureScheduleMatchesVisitDate($jadwal, $tanggalKunjungan);
            $this->ensureScheduleIsBookable($jadwal, $tanggalKunjungan);
            $this->ensureAvailableQuota($jadwal, $data['tanggal_kunjungan']);
            $this->ensureNoActiveDuplicate($pasien, (int) $data['dokter_id'], $data['tanggal_kunjungan']);

            return Antrean::create([
                'pasien_id' => $pasien->id,
                'dokter_id' => $data['dokter_id'],
                'jadwal_dokter_id' => $jadwal->id,
                'tanggal_kunjungan' => $data['tanggal_kunjungan'],
                'nomor_antrean' => $this->nextQueueNumber((int) $data['dokter_id'], $data['tanggal_kunjungan']),
                'kode_antrean' => $this->queueCode($data['tanggal_kunjungan']),
                'status' => AntreanStatus::Menunggu->value,
            ]);
        });
    }

    private function ensureScheduleMatchesVisitDate(JadwalDokter $jadwal, Carbon $tanggalKunjungan): void
    {
        $hariKunjungan = $tanggalKunjungan->locale('id')->isoFormat('dddd');

        if ($jadwal->hari !== $hariKunjungan) {
            throw ValidationException::withMessages([
                'jadwal_dokter_id' => 'Jadwal tidak sesuai dengan tanggal kunjungan yang dipilih.',
            ]);
        }
    }

    private function ensureScheduleIsBookable(JadwalDokter $jadwal, Carbon $tanggalKunjungan): void
    {
        if (! $this->isScheduleBookable($jadwal, $tanggalKunjungan)) {
            throw ValidationException::withMessages([
                'jadwal_dokter_id' => 'Sesi jadwal ini sudah tutup dan tidak bisa dipilih untuk hari ini.',
            ]);
        }
    }

    private function isScheduleBookable(JadwalDokter $jadwal, Carbon $tanggalKunjungan): bool
    {
        $now = now($this->timezone());

        if (! $tanggalKunjungan->isSameDay($now)) {
            return true;
        }

        $jamSelesai = Carbon::parse($tanggalKunjungan->toDateString().' '.$jadwal->jam_selesai, $this->timezone());

        return $now->lt($jamSelesai);
    }

    private function timezone(): string
    {
        return config('app.timezone', 'Asia/Jakarta') ?: 'Asia/Jakarta';
    }

    private function ensureAvailableQuota(JadwalDokter $jadwal, string $tanggal): void
    {
        $terpakai = Antrean::query()
            ->where('jadwal_dokter_id', $jadwal->id)
            ->where('tanggal_kunjungan', $tanggal)
            ->where('status', '!=', AntreanStatus::Batal->value)
            ->lockForUpdate()
            ->get(['id'])
            ->count();

        if ($terpakai >= $jadwal->kuota) {
            throw ValidationException::withMessages([
                'jadwal_dokter_id' => 'Maaf, kuota antrean untuk jadwal ini sudah penuh.',
            ]);
        }
    }

    private function ensureNoActiveDuplicate(Pasien $pasien, int $dokterId, string $tanggal): void
    {
        $activeDuplicate = Antrean::query()
            ->where('pasien_id', $pasien->id)
            ->where('dokter_id', $dokterId)
            ->where('tanggal_kunjungan', $tanggal)
            ->where('status', '!=', AntreanStatus::Batal->value)
            ->lockForUpdate()
            ->first(['id']);

        if ($activeDuplicate) {
            throw ValidationException::withMessages([
                'dokter_id' => 'Anda sudah memiliki antrean aktif untuk dokter ini pada tanggal tersebut.',
            ]);
        }
    }

    private function nextQueueNumber(int $dokterId, string $tanggal): int
    {
        $lastAntrean = Antrean::query()
            ->where('dokter_id', $dokterId)
            ->where('tanggal_kunjungan', $tanggal)
            ->orderByDesc('nomor_antrean')
            ->lockForUpdate()
            ->first();

        return ((int) $lastAntrean?->nomor_antrean) + 1;
    }

    private function queueCode(string $tanggal): string
    {
        do {
            $code = now()->parse($tanggal)->format('Ymd').'-'.Str::upper(Str::random(6));
        } while (Antrean::where('kode_antrean', $code)->exists());

        return $code;
    }
}
