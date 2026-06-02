<?php

namespace Database\Seeders;

use App\Models\Dokter;
use App\Models\JadwalDokter;
use Illuminate\Database\Seeder;

class JadwalPraktekDokterSeeder extends Seeder
{
    public function run(): void
    {
        $dokters = Dokter::query()
            ->where('status_aktif', true)
            ->get();

        if ($dokters->isEmpty()) {
            $dokters = collect([
                Dokter::create([
                    'nama_dokter' => 'Dr. Arief Sazilli Rachmat',
                    'spesialisasi' => 'Dokter Umum',
                    'no_hp' => '081234567801',
                    'status_aktif' => true,
                ]),
            ]);
        }

        $jadwalPraktek = [
            ['hari' => 'Senin', 'jam_mulai' => '08:00', 'jam_selesai' => '13:00', 'kuota' => 20],
            ['hari' => 'Senin', 'jam_mulai' => '18:30', 'jam_selesai' => '21:00', 'kuota' => 20],
            ['hari' => 'Selasa', 'jam_mulai' => '08:00', 'jam_selesai' => '13:00', 'kuota' => 20],
            ['hari' => 'Selasa', 'jam_mulai' => '18:30', 'jam_selesai' => '21:00', 'kuota' => 20],
            ['hari' => 'Rabu', 'jam_mulai' => '08:00', 'jam_selesai' => '13:00', 'kuota' => 20],
            ['hari' => 'Rabu', 'jam_mulai' => '18:30', 'jam_selesai' => '21:00', 'kuota' => 20],
            ['hari' => 'Kamis', 'jam_mulai' => '08:00', 'jam_selesai' => '13:00', 'kuota' => 20],
            ['hari' => 'Sabtu', 'jam_mulai' => '08:00', 'jam_selesai' => '13:00', 'kuota' => 20],
        ];

        foreach ($dokters as $dokter) {
            JadwalDokter::query()
                ->where('dokter_id', $dokter->id)
                ->delete();

            foreach ($jadwalPraktek as $jadwal) {
                JadwalDokter::create(array_merge([
                    'dokter_id' => $dokter->id,
                ], $jadwal));
            }
        }
    }
}
