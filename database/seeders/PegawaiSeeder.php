<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use Illuminate\Database\Seeder;

class PegawaiSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->pegawais() as $pegawai) {
            Pegawai::updateOrCreate(
                ['nama_pegawai' => $pegawai['nama_pegawai']],
                $pegawai,
            );
        }
    }

    private function pegawais(): array
    {
        return [
            ['nama_pegawai' => 'Dewi Rahayu', 'jabatan' => 'Perawat', 'no_hp' => '081234567810'],
            ['nama_pegawai' => 'Rizal Firmansyah', 'jabatan' => 'Petugas Apotek', 'no_hp' => '081234567811'],
            ['nama_pegawai' => 'Nurhayati', 'jabatan' => 'Petugas Pendaftaran', 'no_hp' => '081234567812'],
        ];
    }
}
