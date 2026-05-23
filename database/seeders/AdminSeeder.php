<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@klinikarridlo.com'],
            [
                'name'     => 'Administrator Klinik',
                'email'    => 'admin@klinikarridlo.com',
                'password' => Hash::make('Admin@Klinik123'),
                'role'     => 'admin',
                'no_hp'    => '081234567890',
            ]
        );

        $this->command->info('✅ Admin seeder berhasil dijalankan.');
        $this->command->info('   Email   : admin@klinikarridlo.com');
        $this->command->info('   Password: Admin@Klinik123');
    }
}
