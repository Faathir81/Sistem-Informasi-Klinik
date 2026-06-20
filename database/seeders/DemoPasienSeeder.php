<?php

namespace Database\Seeders;

use App\Models\Pasien;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoPasienSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->pasienUsers() as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('Pasien@123'),
                    'role' => 'pasien',
                    'no_hp' => $data['no_hp'],
                ],
            );

            Pasien::updateOrCreate(
                ['nik' => $data['nik']],
                [
                    'user_id' => $user->id,
                    'nama_pasien' => $data['name'],
                    'tgl_lahir' => $data['tgl_lahir'],
                    'jenis_kelamin' => $data['jk'],
                    'alamat' => $data['alamat'],
                    'no_hp' => $data['no_hp'],
                ],
            );
        }
    }

    private function pasienUsers(): array
    {
        return [
            ['name' => 'Budi Prasetyo', 'email' => 'budi@mail.com', 'nik' => '3201010101010001', 'tgl_lahir' => '1990-05-15', 'jk' => 'Laki-laki', 'alamat' => 'Jl. Mawar No. 10, Jakarta Timur', 'no_hp' => '08111111111'],
            ['name' => 'Sari Dewi', 'email' => 'sari@mail.com', 'nik' => '3201010101010002', 'tgl_lahir' => '1995-08-22', 'jk' => 'Perempuan', 'alamat' => 'Jl. Melati No. 5, Jakarta Timur', 'no_hp' => '08122222222'],
            ['name' => 'Muhammad Hasan', 'email' => 'hasan@mail.com', 'nik' => '3201010101010003', 'tgl_lahir' => '1985-03-10', 'jk' => 'Laki-laki', 'alamat' => 'Jl. Kenanga No. 7, Jakarta Timur', 'no_hp' => '08133333333'],
            ['name' => 'Rina Susanti', 'email' => 'rina@mail.com', 'nik' => '3201010101010004', 'tgl_lahir' => '2000-11-30', 'jk' => 'Perempuan', 'alamat' => 'Jl. Dahlia No. 3, Jakarta Timur', 'no_hp' => '08144444444'],
            ['name' => 'Agus Setiawan', 'email' => 'agus@mail.com', 'nik' => '3201010101010005', 'tgl_lahir' => '1978-07-04', 'jk' => 'Laki-laki', 'alamat' => 'Jl. Anggrek No. 12, Jakarta Timur', 'no_hp' => '08155555555'],
        ];
    }
}
