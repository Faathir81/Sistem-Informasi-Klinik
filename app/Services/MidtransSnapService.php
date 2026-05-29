<?php

namespace App\Services;

use App\Enums\TransaksiStatus;
use App\Models\Pemeriksaan;
use App\Models\PengajuanPasien;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class MidtransSnapService
{
    public const REGISTRATION_FEE = 1000;

    public function createTransaction(Pemeriksaan $pemeriksaan, float $amount): Transaksi
    {
        $serverKey = config('services.midtrans.server_key');

        if (! $serverKey) {
            throw new RuntimeException('MIDTRANS_SERVER_KEY belum dikonfigurasi.');
        }

        $transaksi = Transaksi::updateOrCreate(
            ['pemeriksaan_id' => $pemeriksaan->id],
            [
                'order_id' => 'KLINIK-'.$pemeriksaan->id.'-'.Str::upper(Str::random(8)),
                'amount' => $amount,
                'status' => TransaksiStatus::Pending->value,
                'payment_type' => null,
                'tgl_bayar' => null,
            ],
        );

        $payload = [
            'transaction_details' => [
                'order_id' => $transaksi->order_id,
                'gross_amount' => (int) round($amount),
            ],
            'customer_details' => [
                'first_name' => $pemeriksaan->pasien->nama_pasien,
                'email' => $pemeriksaan->pasien->user?->email,
                'phone' => $pemeriksaan->pasien->no_hp,
            ],
            'item_details' => [
                [
                    'id' => 'PEMERIKSAAN-'.$pemeriksaan->id,
                    'price' => (int) round($amount),
                    'quantity' => 1,
                    'name' => 'Tagihan Klinik Ar-Ridlo',
                ],
            ],
            'enabled_payments' => $this->enabledPayments(),
            'callbacks' => [
                'finish' => route('pasien.pembayaran.index'),
            ],
        ];

        $response = Http::withBasicAuth($serverKey, '')
            ->acceptJson()
            ->asJson()
            ->post($this->snapEndpoint(), $payload);

        if ($response->failed()) {
            throw new RuntimeException('Midtrans gagal membuat transaksi: '.$response->body());
        }

        $transaksi->update([
            'snap_token' => $response->json('token'),
            'snap_url' => $response->json('redirect_url'),
        ]);

        return $transaksi->fresh('pemeriksaan');
    }

    public function createRegistrationTransaction(PengajuanPasien $pengajuan): Transaksi
    {
        $serverKey = config('services.midtrans.server_key');

        if (! $serverKey) {
            throw new RuntimeException('MIDTRANS_SERVER_KEY belum dikonfigurasi.');
        }

        $transaksi = Transaksi::updateOrCreate(
            ['pengajuan_pasien_id' => $pengajuan->id],
            [
                'pemeriksaan_id' => null,
                'order_id' => 'REG-'.$pengajuan->id.'-'.Str::upper(Str::random(8)),
                'amount' => self::REGISTRATION_FEE,
                'status' => TransaksiStatus::Pending->value,
                'payment_type' => null,
                'tgl_bayar' => null,
            ],
        );

        $payload = [
            'transaction_details' => [
                'order_id' => $transaksi->order_id,
                'gross_amount' => self::REGISTRATION_FEE,
            ],
            'customer_details' => [
                'first_name' => $pengajuan->nama_pasien,
                'email' => $pengajuan->user?->email,
                'phone' => $pengajuan->no_hp,
            ],
            'item_details' => [
                [
                    'id' => 'PENDAFTARAN-PASIEN',
                    'price' => self::REGISTRATION_FEE,
                    'quantity' => 1,
                    'name' => 'Biaya Pendaftaran Pasien Klinik Ar-Ridlo',
                ],
            ],
            'enabled_payments' => $this->enabledPayments(),
            'callbacks' => [
                'finish' => route('pasien.pembayaran.show', $transaksi),
            ],
        ];

        $response = Http::withBasicAuth($serverKey, '')
            ->acceptJson()
            ->asJson()
            ->post($this->snapEndpoint(), $payload);

        if ($response->failed()) {
            throw new RuntimeException('Midtrans gagal membuat transaksi pendaftaran: '.$response->body());
        }

        $transaksi->update([
            'snap_token' => $response->json('token'),
            'snap_url' => $response->json('redirect_url'),
        ]);

        return $transaksi->fresh('pengajuanPasien');
    }

    public function isValidSignature(array $payload): bool
    {
        $serverKey = config('services.midtrans.server_key');

        if (! $serverKey || empty($payload['signature_key'])) {
            return false;
        }

        $expected = hash(
            'sha512',
            ($payload['order_id'] ?? '').
            ($payload['status_code'] ?? '').
            ($payload['gross_amount'] ?? '').
            $serverKey
        );

        return hash_equals($expected, $payload['signature_key']);
    }

    public function clientKey(): ?string
    {
        return config('services.midtrans.client_key');
    }

    private function snapEndpoint(): string
    {
        return config('services.midtrans.is_production')
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
    }

    private function enabledPayments(): array
    {
        return ['gopay', 'qris'];
    }
}
