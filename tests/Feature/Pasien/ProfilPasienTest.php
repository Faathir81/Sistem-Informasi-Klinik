<?php

namespace Tests\Feature\Pasien;

use App\Models\Pasien;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfilPasienTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_owned_family_profile(): void
    {
        $user = User::factory()->create(['role' => 'pasien']);
        $pasien = $this->createPasien($user);

        $response = $this->actingAs($user)->patch(
            route('pasien.profil.update', $pasien),
            $this->profilePayload(['nama_pasien' => 'Nama Diperbarui'])
        );

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('pasien.profil.index', absolute: false));
        $this->assertSame('Nama Diperbarui', $pasien->refresh()->nama_pasien);
    }

    public function test_user_cannot_update_profile_owned_by_another_account(): void
    {
        $owner = User::factory()->create(['role' => 'pasien']);
        $otherUser = User::factory()->create(['role' => 'pasien']);
        $pasien = $this->createPasien($owner);

        $response = $this->actingAs($otherUser)->patch(
            route('pasien.profil.update', $pasien),
            $this->profilePayload(['nama_pasien' => 'Diubah Orang Lain'])
        );

        $response->assertForbidden();
        $this->assertSame('Budi Santoso', $pasien->refresh()->nama_pasien);
    }

    public function test_medical_record_number_stays_internal(): void
    {
        $user = User::factory()->create(['role' => 'pasien']);
        $pasien = $this->createPasien($user);

        $this->assertNotNull($pasien->getRawOriginal('no_rekam_medis'));
        $this->assertArrayNotHasKey('no_rekam_medis', $pasien->toArray());

        $this->actingAs($user)
            ->get(route('pasien.profil.index'))
            ->assertOk()
            ->assertDontSee($pasien->getRawOriginal('no_rekam_medis'));
    }

    private function createPasien(User $user): Pasien
    {
        return Pasien::create([
            'user_id' => $user->id,
            ...$this->profilePayload(),
        ]);
    }

    private function profilePayload(array $overrides = []): array
    {
        return array_merge([
            'nik' => '3201010101010001',
            'nama_pasien' => 'Budi Santoso',
            'tgl_lahir' => '1995-05-12',
            'jenis_kelamin' => 'Laki-laki',
            'alamat' => 'Jl. Sehat No. 1',
            'no_hp' => '081234567890',
        ], $overrides);
    }
}
