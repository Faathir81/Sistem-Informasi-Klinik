<?php

namespace App\Http\Controllers\Pasien;

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
        $pengajuanPasien = $user->latestPengajuanPasien;

        $antreanAktif = null;
        $pemeriksaanTerakhir = null;
        $jumlahAntrean = 0;
        $jumlahRiwayat = 0;
        $tagihanBelumLunas = 0;

        if ($pasien) {
            $antreanAktif = Antrean::query()
                ->with(['dokter', 'jadwalDokter'])
                ->where('pasien_id', $pasien->id)
                ->whereIn('status', ['Menunggu', 'Dipanggil'])
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
                ->where('status_bayar', '!=', 'Lunas')
                ->count();
        }

        return view('pasien.dashboard', compact(
            'user',
            'pasien',
            'pengajuanPasien',
            'antreanAktif',
            'pemeriksaanTerakhir',
            'jumlahAntrean',
            'jumlahRiwayat',
            'tagihanBelumLunas',
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
