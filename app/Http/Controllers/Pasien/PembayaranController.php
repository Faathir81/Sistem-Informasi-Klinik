<?php

namespace App\Http\Controllers\Pasien;

use App\Enums\TransaksiStatus;
use App\Http\Controllers\Controller;
use App\Models\Pemeriksaan;
use App\Models\Transaksi;
use App\Services\MidtransSnapService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PembayaranController extends Controller
{
    public function index(): View
    {
        $pasien = auth()->user()->pasien;

        $pemeriksaans = collect();

        if ($pasien) {
            $pemeriksaans = Pemeriksaan::query()
                ->with(['dokter', 'resep.details.obat', 'transaksi'])
                ->where('pasien_id', $pasien->id)
                ->latest('tgl_pemeriksaan')
                ->get();
        }

        return view('pasien.pembayaran.index', compact('pasien', 'pemeriksaans'));
    }

    public function store(Request $request, Pemeriksaan $pemeriksaan, MidtransSnapService $midtrans): RedirectResponse
    {
        abort_unless($pemeriksaan->pasien?->user_id === auth()->id(), 403);

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:1000'],
        ]);

        if ($pemeriksaan->transaksi?->status === TransaksiStatus::Settlement->value) {
            return redirect()
                ->route('pasien.pembayaran.show', $pemeriksaan->transaksi)
                ->with('status', 'Tagihan ini sudah lunas.');
        }

        try {
            $transaksi = $midtrans->createTransaction($pemeriksaan->load(['pasien.user', 'resep']), (float) $data['amount']);
        } catch (\Throwable $exception) {
            return back()->withErrors([
                'amount' => $exception->getMessage(),
            ]);
        }

        return redirect()
            ->route('pasien.pembayaran.show', $transaksi)
            ->with('status', 'Transaksi pembayaran berhasil dibuat. Silakan lanjutkan pembayaran.');
    }

    public function show(Transaksi $transaksi, MidtransSnapService $midtrans): View
    {
        $transaksi->load('pemeriksaan.pasien', 'pemeriksaan.dokter', 'pemeriksaan.resep', 'pengajuanPasien.user');

        $ownerId = $transaksi->pemeriksaan?->pasien?->user_id
            ?? $transaksi->pengajuanPasien?->user_id;

        abort_unless($ownerId === auth()->id(), 403);

        return view('pasien.pembayaran.show', [
            'transaksi' => $transaksi,
            'clientKey' => $midtrans->clientKey(),
        ]);
    }
}
