<?php

namespace App\Http\Controllers\Pasien;

use App\Enums\AntreanStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Antrean;
use App\Models\Pemeriksaan;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard utama pasien.
     */
    public function index()
    {
        $user = auth()->user();
        $pasiens = $user->pasiens;
        $pasienIds = $pasiens->pluck('id');
        $pasien = $pasiens->first();
        $pengajuanPasien = $user->latestPengajuanPasien?->load('transaksi');

        $antreanAktif = null;
        $pemeriksaanTerakhir = null;
        $jumlahAntrean = 0;
        $jumlahRiwayat = 0;
        $tagihanBelumLunas = 0;

        if ($pasienIds->isNotEmpty()) {
            $antreanAktif = Antrean::query()
                ->with(['pasien', 'dokter', 'jadwalDokter'])
                ->whereIn('pasien_id', $pasienIds)
                ->whereIn('status', AntreanStatus::activeValues())
                ->orderBy('tanggal_kunjungan')
                ->orderBy('nomor_antrean')
                ->first();

            $pemeriksaanTerakhir = Pemeriksaan::query()
                ->with(['pasien', 'dokter', 'resep', 'tindakanDetails'])
                ->whereIn('pasien_id', $pasienIds)
                ->latest('tgl_pemeriksaan')
                ->first();

            $jumlahAntrean = Antrean::whereIn('pasien_id', $pasienIds)->count();
            $jumlahRiwayat = Pemeriksaan::whereIn('pasien_id', $pasienIds)->count();
            $tagihanBelumLunas = Pemeriksaan::whereIn('pasien_id', $pasienIds)
                ->where('status_bayar', '!=', PaymentStatus::Lunas->value)
                ->count();
        }

        $quickActions = [
            [
                'title' => 'Status Antrean',
                'body' => 'Pantau semua riwayat antrean dan buka tiket QR.',
                'route' => $pasien ? route('pasien.antrean.index') : route('pasien.pengajuan-pasien.create'),
                'id' => 'btn-status-antrean-card',
                'action_text' => $pasien ? 'Buka' : 'Lengkapi Data',
            ],
            [
                'title' => 'Riwayat Medis',
                'body' => 'Lihat diagnosa, tindakan, dan resep obat Anda.',
                'route' => $pasien ? route('pasien.riwayat.index') : route('pasien.pengajuan-pasien.create'),
                'id' => 'btn-riwayat-medis-card',
                'action_text' => $pasien ? 'Buka' : 'Lengkapi Data',
            ],
            [
                'title' => 'Pembayaran QRIS',
                'body' => 'Buat transaksi pembayaran secara online.',
                'route' => $pasien ? route('pasien.pembayaran.index') : route('pasien.pengajuan-pasien.create'),
                'id' => 'btn-pembayaran-card',
                'action_text' => $pasien ? 'Buka' : 'Lengkapi Data',
            ],
        ];

        return view('pasien.dashboard', compact(
            'user',
            'pasien',
            'pasiens',
            'pengajuanPasien',
            'antreanAktif',
            'pemeriksaanTerakhir',
            'jumlahAntrean',
            'jumlahRiwayat',
            'tagihanBelumLunas',
            'quickActions',
        ));
    }

    public function riwayat()
    {
        $pasiens = auth()->user()->pasiens;
        $pasienIds = $pasiens->pluck('id');
        $pasien = $pasiens->first();

        $pemeriksaans = collect();

        if ($pasienIds->isNotEmpty()) {
            $pemeriksaans = Pemeriksaan::query()
                ->with(['pasien', 'dokter', 'resep.details.obat', 'tindakanDetails'])
                ->whereIn('pasien_id', $pasienIds)
                ->latest('tgl_pemeriksaan')
                ->get();
        }

        return view('pasien.riwayat.index', compact('pasien', 'pasiens', 'pemeriksaans'));
    }
}
