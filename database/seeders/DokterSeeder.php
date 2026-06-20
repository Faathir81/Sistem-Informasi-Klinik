<?php

namespace Database\Seeders;

use App\Models\Dokter;
use Illuminate\Database\Seeder;

class DokterSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->dokters() as $dokter) {
            Dokter::updateOrCreate(
                ['nama_dokter' => $dokter['nama_dokter']],
                $dokter,
            );
        }
    }

    private function dokters(): array
    {
        return [
            ['nama_dokter' => 'Dr. Arief Sazilli Rachmat', 'spesialisasi' => 'Dokter Umum', 'no_hp' => '081234567801', 'status_aktif' => true],
            ['nama_dokter' => 'Asisten Dr. Arief', 'spesialisasi' => 'Asisten Dokter', 'no_hp' => '081234567802', 'status_aktif' => true],
        ];
    }
}
