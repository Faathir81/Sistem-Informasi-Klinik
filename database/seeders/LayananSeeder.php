<?php

namespace Database\Seeders;

use App\Models\Layanan;
use Illuminate\Database\Seeder;

class LayananSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->layanans() as $layanan) {
            Layanan::updateOrCreate(
                ['nama_layanan' => $layanan['nama_layanan']],
                $layanan,
            );
        }
    }

    private function layanans(): array
    {
        return [
            ['nama_layanan' => 'Pengurutan', 'tarif_default' => 50000, 'status_aktif' => true],
            ['nama_layanan' => 'Bekam', 'tarif_default' => 50000, 'status_aktif' => true],
            ['nama_layanan' => 'Cek Tekanan Darah', 'tarif_default' => 10000, 'status_aktif' => true],
        ];
    }
}
