<?php

namespace App\Http\Controllers\Pasien;

use App\Enums\PengajuanPasienStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePengajuanPasienRequest;
use App\Models\PengajuanPasien;
use App\Services\MidtransSnapService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PengajuanPasienController extends Controller
{
    public function create(): View|RedirectResponse
    {
        $user = auth()->user()->load('latestPengajuanPasien.transaksi', 'pasien');

        if ($user->pasien) {
            return redirect()
                ->route('pasien.dashboard')
                ->with('success', 'Data pasien Anda sudah terdaftar.');
        }

        $pengajuan = $user->latestPengajuanPasien;

        if ($pengajuan && in_array($pengajuan->status, [
            PengajuanPasienStatus::MenungguPembayaran->value,
            PengajuanPasienStatus::Menunggu->value,
        ], true)) {
            if ($pengajuan->transaksi) {
                return redirect()
                    ->route('pasien.pembayaran.show', $pengajuan->transaksi)
                    ->with('status', 'Selesaikan pembayaran pendaftaran Rp1.000 agar akun pasien otomatis aktif.');
            }

            return redirect()->route('pasien.dashboard')
                ->with('error', 'Pengajuan Anda menunggu pembayaran, tetapi transaksi belum tersedia. Silakan kirim ulang pengajuan.');
        }

        return view('pasien.pengajuan.create', compact('user', 'pengajuan'));
    }

    public function store(StorePengajuanPasienRequest $request, MidtransSnapService $midtrans): RedirectResponse
    {
        $user = auth()->user()->load('pasien', 'latestPengajuanPasien.transaksi');

        if ($user->pasien) {
            return redirect()
                ->route('pasien.dashboard')
                ->with('success', 'Data pasien Anda sudah terdaftar.');
        }

        $pendingPengajuan = PengajuanPasien::where('user_id', $user->id)
            ->whereIn('status', [
                PengajuanPasienStatus::MenungguPembayaran->value,
                PengajuanPasienStatus::Menunggu->value,
            ])
            ->exists();

        if ($pendingPengajuan) {
            $transaksi = $user->latestPengajuanPasien?->transaksi;

            return $transaksi
                ? redirect()->route('pasien.pembayaran.show', $transaksi)
                : redirect()->route('pasien.dashboard')->with('error', 'Pengajuan Anda masih menunggu pembayaran.');
        }

        $data = $request->validated();

        $nikSedangDiajukan = PengajuanPasien::where('nik', $data['nik'])
            ->whereIn('status', [
                PengajuanPasienStatus::Menunggu->value,
                PengajuanPasienStatus::MenungguPembayaran->value,
                PengajuanPasienStatus::Disetujui->value,
            ])
            ->exists();

        if ($nikSedangDiajukan) {
            return back()
                ->withInput()
                ->withErrors(['nik' => 'NIK ini sedang diajukan atau sudah disetujui.']);
        }

        $pengajuan = PengajuanPasien::create([
            ...$data,
            'user_id' => $user->id,
            'status' => PengajuanPasienStatus::MenungguPembayaran->value,
        ]);

        try {
            $transaksi = $midtrans->createRegistrationTransaction($pengajuan->load('user'));
        } catch (\Throwable $exception) {
            $pengajuan->markPaymentFailed();

            return back()
                ->withInput()
                ->withErrors(['nik' => $exception->getMessage()]);
        }

        return redirect()
            ->route('pasien.pembayaran.show', $transaksi)
            ->with('status', 'Pengajuan tersimpan. Selesaikan pembayaran pendaftaran Rp1.000 agar akun pasien otomatis aktif.');
    }
}
