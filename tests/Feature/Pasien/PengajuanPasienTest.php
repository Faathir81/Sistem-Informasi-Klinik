<?php

namespace Tests\Feature\Pasien;

use App\Models\Pasien;
use App\Models\PengajuanPasien;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PengajuanPasienTest extends TestCase
{
    use RefreshDatabase;

    public function test_pasien_can_submit_pengajuan_when_profile_is_not_registered(): void
    {
        $user = User::factory()->create([
            'role' => 'pasien',
            'no_hp' => '081234567890',
        ]);

        $response = $this->actingAs($user)
            ->post(route('pasien.pengajuan-pasien.store'), $this->validPayload());

        $response->assertRedirect(route('pasien.dashboard', absolute: false));

        $this->assertDatabaseHas('pengajuan_pasiens', [
            'user_id' => $user->id,
            'nik' => '3201010101010001',
            'status' => 'Menunggu',
        ]);

        $this->assertDatabaseMissing('pasiens', [
            'user_id' => $user->id,
            'nik' => '3201010101010001',
        ]);
    }

    public function test_pasien_cannot_submit_another_pengajuan_while_pending(): void
    {
        $user = User::factory()->create(['role' => 'pasien']);

        PengajuanPasien::create($this->validPayload([
            'user_id' => $user->id,
            'status' => 'Menunggu',
        ]));

        $response = $this->actingAs($user)
            ->post(route('pasien.pengajuan-pasien.store'), $this->validPayload([
                'nik' => '3201010101010002',
            ]));

        $response->assertRedirect(route('pasien.dashboard', absolute: false));
        $this->assertDatabaseCount('pengajuan_pasiens', 1);
    }

    public function test_admin_approval_creates_patient_record_and_links_reviewer(): void
    {
        $user = User::factory()->create(['role' => 'pasien']);
        $admin = User::factory()->create(['role' => 'admin']);
        $pengajuan = PengajuanPasien::create($this->validPayload([
            'user_id' => $user->id,
            'status' => 'Menunggu',
        ]));

        $pasien = $pengajuan->approve($admin);

        $this->assertNotNull($pasien->no_rekam_medis);

        $this->assertDatabaseHas('pasiens', [
            'id' => $pasien->id,
            'user_id' => $user->id,
            'nik' => '3201010101010001',
            'nama_pasien' => 'Budi Santoso',
        ]);

        $this->assertDatabaseHas('pengajuan_pasiens', [
            'id' => $pengajuan->id,
            'status' => 'Disetujui',
            'pasien_id' => $pasien->id,
            'reviewed_by' => $admin->id,
        ]);
    }

    public function test_rejected_pengajuan_can_be_submitted_again(): void
    {
        $user = User::factory()->create(['role' => 'pasien']);
        $admin = User::factory()->create(['role' => 'admin']);
        $pengajuan = PengajuanPasien::create($this->validPayload([
            'user_id' => $user->id,
            'status' => 'Menunggu',
        ]));

        $pengajuan->reject($admin, 'NIK kurang jelas.');

        $response = $this->actingAs($user)
            ->post(route('pasien.pengajuan-pasien.store'), $this->validPayload([
                'alamat' => 'Jl. Sehat No. 2',
                'catatan_pasien' => 'Data sudah diperbaiki.',
            ]));

        $response->assertRedirect(route('pasien.dashboard', absolute: false));

        $this->assertDatabaseCount('pengajuan_pasiens', 2);
        $this->assertDatabaseHas('pengajuan_pasiens', [
            'user_id' => $user->id,
            'nik' => '3201010101010001',
            'status' => 'Menunggu',
            'alamat' => 'Jl. Sehat No. 2',
        ]);
    }

    public function test_registered_patient_is_redirected_from_pengajuan_form(): void
    {
        $user = User::factory()->create(['role' => 'pasien']);

        Pasien::create([
            'user_id' => $user->id,
            'nik' => '3201010101010001',
            'nama_pasien' => 'Budi Santoso',
            'tgl_lahir' => '1995-05-12',
            'jenis_kelamin' => 'Laki-laki',
            'alamat' => 'Jl. Sehat No. 1',
            'no_hp' => '081234567890',
        ]);

        $response = $this->actingAs($user)
            ->get(route('pasien.pengajuan-pasien.create'));

        $response->assertRedirect(route('pasien.dashboard', absolute: false));
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'nik' => '3201010101010001',
            'nama_pasien' => 'Budi Santoso',
            'tgl_lahir' => '1995-05-12',
            'jenis_kelamin' => 'Laki-laki',
            'alamat' => 'Jl. Sehat No. 1',
            'no_hp' => '081234567890',
            'catatan_pasien' => null,
        ], $overrides);
    }
}
