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
        $pasien = $user->pasien;
        $pengajuanPasien = $user->latestPengajuanPasien?->load('transaksi');

        $antreanAktif = null;
        $pemeriksaanTerakhir = null;
        $jumlahAntrean = 0;
        $jumlahRiwayat = 0;
        $tagihanBelumLunas = 0;

        if ($pasien) {
            $antreanAktif = Antrean::query()
                ->with(['dokter', 'jadwalDokter'])
                ->where('pasien_id', $pasien->id)
                ->whereIn('status', AntreanStatus::activeValues())
                ->orderBy('tanggal_kunjungan')
                ->orderBy('nomor_antrean')
                ->first();

            $pemeriksaanTerakhir = Pemeriksaan::query()
                ->with(['dokter', 'resep'])
                ->where('pasien_id', $pasien->id)
                ->latest('tgl_pemeriksaan')
                ->first();

            $jumlahAntrean = Antrean::where('pasien_id', $pasien->id)->count();
            $jumlahRiwayat = Pemeriksaan::where('pasien_id', $pasien->id)->count();
            $tagihanBelumLunas = Pemeriksaan::where('pasien_id', $pasien->id)
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
        $pasien = auth()->user()->pasien;

        $pemeriksaans = collect();

        if ($pasien) {
            $pemeriksaans = Pemeriksaan::query()
                ->with(['dokter', 'resep.details.obat'])
                ->where('pasien_id', $pasien->id)
                ->latest('tgl_pemeriksaan')
                ->get();
        }

        return view('pasien.riwayat.index', compact('pasien', 'pemeriksaans'));
    }
}
