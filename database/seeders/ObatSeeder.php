<?php

namespace Database\Seeders;

use App\Models\Obat;
use App\Models\StokObat;
use Illuminate\Database\Seeder;

class ObatSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->obats() as $data) {
            $obat = Obat::updateOrCreate(
                ['nama_obat' => $data['nama_obat']],
                [
                    'satuan' => $data['satuan'],
                    'harga_jual' => $data['harga_jual'],
                ],
            );

            StokObat::firstOrCreate(
                [
                    'obat_id' => $obat->id,
                    'batch' => $data['batch'],
                    'tgl_kadaluarsa' => $data['tgl_kadaluarsa'],
                ],
                [
                    'harga_beli' => $data['harga_beli'],
                    'stok' => $data['stok'],
                ],
            );
        }
    }

    private function obats(): array
    {
        return [
            ['nama_obat' => 'Paracetamol 500mg', 'satuan' => 'Tablet', 'harga_jual' => 1500, 'stok' => 120, 'harga_beli' => 800, 'tgl_kadaluarsa' => '2027-05-24', 'batch' => 'PCT-202705'],
            ['nama_obat' => 'Amoxicillin 500mg', 'satuan' => 'Kapsul', 'harga_jual' => 3000, 'stok' => 80, 'harga_beli' => 1800, 'tgl_kadaluarsa' => '2027-02-15', 'batch' => 'AMX-202702'],
            ['nama_obat' => 'Cetirizine 10mg', 'satuan' => 'Tablet', 'harga_jual' => 2000, 'stok' => 60, 'harga_beli' => 900, 'tgl_kadaluarsa' => '2026-12-10', 'batch' => 'CTZ-202612'],
            ['nama_obat' => 'Vitamin C 500mg', 'satuan' => 'Tablet', 'harga_jual' => 2500, 'stok' => 45, 'harga_beli' => 1000, 'tgl_kadaluarsa' => '2027-08-01', 'batch' => 'VTC-202708'],
            ['nama_obat' => 'Antasida Doen', 'satuan' => 'Tablet', 'harga_jual' => 1500, 'stok' => 8, 'harga_beli' => 700, 'tgl_kadaluarsa' => '2026-06-20', 'batch' => 'ANT-202606'],
        ];
    }
}
