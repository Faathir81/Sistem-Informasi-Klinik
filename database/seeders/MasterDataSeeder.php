<?php

namespace Database\Seeders;

use App\Models\Dokter;
use App\Models\JadwalDokter;
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
        $dokter1 = Dokter::create([
            'nama_dokter'  => 'dr. Ahmad Rivai, Sp.U',
            'spesialisasi' => 'Dokter Umum',
            'no_hp'        => '081234567801',
            'status_aktif' => true,
        ]);

        $dokter2 = Dokter::create([
            'nama_dokter'  => 'dr. Siti Nuraini, M.Kes',
            'spesialisasi' => 'Dokter Umum',
            'no_hp'        => '081234567802',
            'status_aktif' => true,
        ]);

        $dokter3 = Dokter::create([
            'nama_dokter'  => 'dr. Budi Santoso',
            'spesialisasi' => 'Dokter Gizi Klinik',
            'no_hp'        => '081234567803',
            'status_aktif' => true,
        ]);

        // ─── 2. JADWAL PRAKTEK ───────────────────────────────────────────────
        // Jadwal dr. Ahmad Rivai
        $jadwals = [
            ['dokter_id' => $dokter1->id, 'hari' => 'Senin',   'jam_mulai' => '08:00', 'jam_selesai' => '12:00', 'kuota' => 20],
            ['dokter_id' => $dokter1->id, 'hari' => 'Rabu',    'jam_mulai' => '08:00', 'jam_selesai' => '12:00', 'kuota' => 20],
            ['dokter_id' => $dokter1->id, 'hari' => 'Jumat',   'jam_mulai' => '08:00', 'jam_selesai' => '12:00', 'kuota' => 20],
            // Jadwal dr. Siti Nuraini
            ['dokter_id' => $dokter2->id, 'hari' => 'Selasa',  'jam_mulai' => '09:00', 'jam_selesai' => '14:00', 'kuota' => 15],
            ['dokter_id' => $dokter2->id, 'hari' => 'Kamis',   'jam_mulai' => '09:00', 'jam_selesai' => '14:00', 'kuota' => 15],
            ['dokter_id' => $dokter2->id, 'hari' => 'Sabtu',   'jam_mulai' => '09:00', 'jam_selesai' => '13:00', 'kuota' => 10],
            // Jadwal dr. Budi Santoso
            ['dokter_id' => $dokter3->id, 'hari' => 'Senin',   'jam_mulai' => '14:00', 'jam_selesai' => '18:00', 'kuota' => 12],
            ['dokter_id' => $dokter3->id, 'hari' => 'Kamis',   'jam_mulai' => '14:00', 'jam_selesai' => '18:00', 'kuota' => 12],
        ];

        foreach ($jadwals as $jadwal) {
            JadwalDokter::create($jadwal);
        }

        // ─── 3. DATA PEGAWAI ─────────────────────────────────────────────────
        Pegawai::create([
            'nama_pegawai' => 'Dewi Rahayu',
            'jabatan'      => 'Perawat',
            'no_hp'        => '081234567810',
        ]);

        Pegawai::create([
            'nama_pegawai' => 'Rizal Firmansyah',
            'jabatan'      => 'Petugas Apotek',
            'no_hp'        => '081234567811',
        ]);

        Pegawai::create([
            'nama_pegawai' => 'Nurhayati',
            'jabatan'      => 'Petugas Pendaftaran',
            'no_hp'        => '081234567812',
        ]);

        // ─── 4. DATA PASIEN (+ akun user pasien) ─────────────────────────────
        $pasienUsers = [
            ['name' => 'Budi Prasetyo',    'email' => 'budi@mail.com',    'nik' => '3201010101010001', 'tgl_lahir' => '1990-05-15', 'jk' => 'Laki-laki',  'alamat' => 'Jl. Mawar No. 10, Jakarta Timur',  'no_hp' => '08111111111'],
            ['name' => 'Sari Dewi',        'email' => 'sari@mail.com',    'nik' => '3201010101010002', 'tgl_lahir' => '1995-08-22', 'jk' => 'Perempuan',  'alamat' => 'Jl. Melati No. 5, Jakarta Timur',   'no_hp' => '08122222222'],
            ['name' => 'Muhammad Hasan',   'email' => 'hasan@mail.com',   'nik' => '3201010101010003', 'tgl_lahir' => '1985-03-10', 'jk' => 'Laki-laki',  'alamat' => 'Jl. Kenanga No. 7, Jakarta Timur',  'no_hp' => '08133333333'],
            ['name' => 'Rina Susanti',     'email' => 'rina@mail.com',    'nik' => '3201010101010004', 'tgl_lahir' => '2000-11-30', 'jk' => 'Perempuan',  'alamat' => 'Jl. Dahlia No. 3, Jakarta Timur',   'no_hp' => '08144444444'],
            ['name' => 'Agus Setiawan',    'email' => 'agus@mail.com',    'nik' => '3201010101010005', 'tgl_lahir' => '1978-07-04', 'jk' => 'Laki-laki',  'alamat' => 'Jl. Anggrek No. 12, Jakarta Timur', 'no_hp' => '08155555555'],
        ];

        foreach ($pasienUsers as $data) {
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make('Pasien@123'),
                'role'     => 'pasien',
                'no_hp'    => $data['no_hp'],
            ]);

            Pasien::create([
                'user_id'       => $user->id,
                'nik'           => $data['nik'],
                'nama_pasien'   => $data['name'],
                'tgl_lahir'     => $data['tgl_lahir'],
                'jenis_kelamin' => $data['jk'],
                'alamat'        => $data['alamat'],
                'no_hp'         => $data['no_hp'],
            ]);
        }
    }
}
