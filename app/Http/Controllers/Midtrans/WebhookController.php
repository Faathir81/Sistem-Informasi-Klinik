<?php

namespace App\Http\Controllers\Midtrans;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Services\MidtransSnapService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function __invoke(Request $request, MidtransSnapService $midtrans): JsonResponse
    {
        $payload = $request->all();

        if (! $midtrans->isValidSignature($payload)) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $transaksi = Transaksi::where('order_id', $payload['order_id'] ?? null)->firstOrFail();
        $transactionStatus = strtoupper((string) ($payload['transaction_status'] ?? ''));

        match ($transactionStatus) {
            'SETTLEMENT', 'CAPTURE' => $transaksi->markSettled($payload['payment_type'] ?? null),
            'EXPIRE' => $transaksi->update(['status' => 'EXPIRE']),
            'CANCEL', 'DENY', 'FAILURE' => $transaksi->update(['status' => 'CANCEL']),
            default => $transaksi->update(['status' => 'PENDING']),
        };

        return response()->json(['message' => 'OK']);
    }
}
