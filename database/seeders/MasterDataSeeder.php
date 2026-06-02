<?php

namespace Database\Seeders;

use App\Models\Dokter;
use App\Models\Layanan;
use App\Models\Obat;
use App\Models\Pasien;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // ─── 1. DATA DOKTER ──────────────────────────────────────────────────
        Dokter::create([
            'nama_dokter' => 'Dr. Arief Sazilli Rachmat',
            'spesialisasi' => 'Dokter Umum',
            'no_hp' => '081234567801',
            'status_aktif' => true,
        ]);

        Dokter::create([
            'nama_dokter' => 'Asisten Dr. Arief',
            'spesialisasi' => 'Asisten Dokter',
            'no_hp' => '081234567802',
            'status_aktif' => true,
        ]);

        // ─── 2. DATA PEGAWAI ─────────────────────────────────────────────────
        Pegawai::create([
            'nama_pegawai' => 'Dewi Rahayu',
            'jabatan' => 'Perawat',
            'no_hp' => '081234567810',
        ]);

        Pegawai::create([
            'nama_pegawai' => 'Rizal Firmansyah',
            'jabatan' => 'Petugas Apotek',
            'no_hp' => '081234567811',
        ]);

        Pegawai::create([
            'nama_pegawai' => 'Nurhayati',
            'jabatan' => 'Petugas Pendaftaran',
            'no_hp' => '081234567812',
        ]);

        // ─── 3. DATA PASIEN (+ akun user pasien) ─────────────────────────────
        $pasienUsers = [
            ['name' => 'Budi Prasetyo',    'email' => 'budi@mail.com',    'nik' => '3201010101010001', 'tgl_lahir' => '1990-05-15', 'jk' => 'Laki-laki',  'alamat' => 'Jl. Mawar No. 10, Jakarta Timur',  'no_hp' => '08111111111'],
            ['name' => 'Sari Dewi',        'email' => 'sari@mail.com',    'nik' => '3201010101010002', 'tgl_lahir' => '1995-08-22', 'jk' => 'Perempuan',  'alamat' => 'Jl. Melati No. 5, Jakarta Timur',   'no_hp' => '08122222222'],
            ['name' => 'Muhammad Hasan',   'email' => 'hasan@mail.com',   'nik' => '3201010101010003', 'tgl_lahir' => '1985-03-10', 'jk' => 'Laki-laki',  'alamat' => 'Jl. Kenanga No. 7, Jakarta Timur',  'no_hp' => '08133333333'],
            ['name' => 'Rina Susanti',     'email' => 'rina@mail.com',    'nik' => '3201010101010004', 'tgl_lahir' => '2000-11-30', 'jk' => 'Perempuan',  'alamat' => 'Jl. Dahlia No. 3, Jakarta Timur',   'no_hp' => '08144444444'],
            ['name' => 'Agus Setiawan',    'email' => 'agus@mail.com',    'nik' => '3201010101010005', 'tgl_lahir' => '1978-07-04', 'jk' => 'Laki-laki',  'alamat' => 'Jl. Anggrek No. 12, Jakarta Timur', 'no_hp' => '08155555555'],
        ];

        foreach ($pasienUsers as $data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('Pasien@123'),
                'role' => 'pasien',
                'no_hp' => $data['no_hp'],
            ]);

            Pasien::create([
                'user_id' => $user->id,
                'nik' => $data['nik'],
                'nama_pasien' => $data['name'],
                'tgl_lahir' => $data['tgl_lahir'],
                'jenis_kelamin' => $data['jk'],
                'alamat' => $data['alamat'],
                'no_hp' => $data['no_hp'],
            ]);
        }

        // ─── 4. DATA LAYANAN / TINDAKAN KLINIK ──────────────────────────────
        $layanans = [
            ['nama_layanan' => 'Pengurutan', 'tarif_default' => 50000, 'status_aktif' => true],
            ['nama_layanan' => 'Bekam', 'tarif_default' => 50000, 'status_aktif' => true],
            ['nama_layanan' => 'Cek Tekanan Darah', 'tarif_default' => 10000, 'status_aktif' => true],
        ];

        foreach ($layanans as $layanan) {
            Layanan::updateOrCreate(
                ['nama_layanan' => $layanan['nama_layanan']],
                $layanan,
            );
        }

        // ─── 5. DATA OBAT AWAL ───────────────────────────────────────────────
        $obats = [
            ['nama_obat' => 'Paracetamol 500mg', 'satuan' => 'Tablet', 'stok' => 120, 'harga_beli' => 800, 'harga_jual' => 1500, 'tgl_kadaluarsa' => '2027-05-24'],
            ['nama_obat' => 'Amoxicillin 500mg', 'satuan' => 'Kapsul', 'stok' => 80, 'harga_beli' => 1800, 'harga_jual' => 3000, 'tgl_kadaluarsa' => '2027-02-15'],
            ['nama_obat' => 'Cetirizine 10mg', 'satuan' => 'Tablet', 'stok' => 60, 'harga_beli' => 900, 'harga_jual' => 2000, 'tgl_kadaluarsa' => '2026-12-10'],
            ['nama_obat' => 'Vitamin C 500mg', 'satuan' => 'Tablet', 'stok' => 45, 'harga_beli' => 1000, 'harga_jual' => 2500, 'tgl_kadaluarsa' => '2027-08-01'],
            ['nama_obat' => 'Antasida Doen', 'satuan' => 'Tablet', 'stok' => 8, 'harga_beli' => 700, 'harga_jual' => 1500, 'tgl_kadaluarsa' => '2026-06-20'],
        ];

        foreach ($obats as $obat) {
            Obat::create($obat);
        }
    }
}
