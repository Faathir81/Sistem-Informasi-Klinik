<?php

namespace App\Services;

use App\Models\Pemeriksaan;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class MidtransSnapService
{
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
                'status' => 'PENDING',
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
            'enabled_payments' => ['qris', 'gopay', 'shopeepay'],
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
}
