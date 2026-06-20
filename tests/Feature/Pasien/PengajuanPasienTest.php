<?php

namespace Tests\Feature\Pasien;

use App\Enums\PengajuanPasienStatus;
use App\Enums\TransaksiStatus;
use App\Models\Pasien;
use App\Models\PengajuanPasien;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
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

        Http::fake(['*' => Http::response([
            'token' => 'snap-token',
            'redirect_url' => 'https://midtrans.test/pay',
        ])]);
        config(['services.midtrans.server_key' => 'server-key']);

        $response = $this->actingAs($user)
            ->post(route('pasien.pengajuan-pasien.store'), $this->validPayload());

        $pengajuan = PengajuanPasien::firstOrFail();
        $transaksi = $pengajuan->transaksi;

        $response->assertRedirect(route('pasien.pembayaran.show', $transaksi, absolute: false));

        $this->assertDatabaseHas('pengajuan_pasiens', [
            'user_id' => $user->id,
            'nik' => '3201010101010001',
            'status' => PengajuanPasienStatus::MenungguPembayaran->value,
        ]);

        $this->assertDatabaseHas('transaksis', [
            'pengajuan_pasien_id' => $pengajuan->id,
            'amount' => 1000,
            'status' => TransaksiStatus::Pending->value,
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
            'status' => PengajuanPasienStatus::MenungguPembayaran->value,
        ]));

        $response = $this->actingAs($user)
            ->post(route('pasien.pengajuan-pasien.store'), $this->validPayload([
                'nik' => '3201010101010002',
            ]));

        $response->assertRedirect(route('pasien.dashboard', absolute: false));
        $this->assertDatabaseCount('pengajuan_pasiens', 1);
    }

    public function test_settled_registration_payment_creates_patient_record(): void
    {
        $user = User::factory()->create(['role' => 'pasien']);
        $pengajuan = PengajuanPasien::create($this->validPayload([
            'user_id' => $user->id,
            'status' => PengajuanPasienStatus::MenungguPembayaran->value,
        ]));
        $transaksi = Transaksi::create([
            'pengajuan_pasien_id' => $pengajuan->id,
            'order_id' => 'REG-TEST',
            'amount' => 1000,
            'status' => TransaksiStatus::Pending->value,
        ]);

        $transaksi->markSettled('qris');
        $pasien = $pengajuan->refresh()->pasien;

        $this->assertNotNull($pasien->no_rekam_medis);

        $this->assertDatabaseHas('pasiens', [
            'id' => $pasien->id,
            'user_id' => $user->id,
            'nik' => '3201010101010001',
            'nama_pasien' => 'Budi Santoso',
        ]);

        $this->assertDatabaseHas('pengajuan_pasiens', [
            'id' => $pengajuan->id,
            'status' => PengajuanPasienStatus::Disetujui->value,
            'pasien_id' => $pasien->id,
        ]);
    }

    public function test_failed_payment_pengajuan_can_be_submitted_again(): void
    {
        $user = User::factory()->create(['role' => 'pasien']);
        $pengajuan = PengajuanPasien::create($this->validPayload([
            'user_id' => $user->id,
            'status' => PengajuanPasienStatus::MenungguPembayaran->value,
        ]));

        $pengajuan->markPaymentFailed();

        Http::fake(['*' => Http::response([
            'token' => 'snap-token',
            'redirect_url' => 'https://midtrans.test/pay',
        ])]);
        config(['services.midtrans.server_key' => 'server-key']);

        $response = $this->actingAs($user)
            ->post(route('pasien.pengajuan-pasien.store'), $this->validPayload([
                'alamat' => 'Jl. Sehat No. 2',
                'catatan_pasien' => 'Data sudah diperbaiki.',
            ]));

        $response->assertRedirect();

        $this->assertDatabaseCount('pengajuan_pasiens', 2);
        $this->assertDatabaseHas('pengajuan_pasiens', [
            'user_id' => $user->id,
            'nik' => '3201010101010001',
            'status' => PengajuanPasienStatus::MenungguPembayaran->value,
            'alamat' => 'Jl. Sehat No. 2',
        ]);
    }

    public function test_registered_patient_can_open_form_to_add_another_family_profile(): void
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

        $response
            ->assertOk()
            ->assertSee('Tambah Profil Pasien');
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
