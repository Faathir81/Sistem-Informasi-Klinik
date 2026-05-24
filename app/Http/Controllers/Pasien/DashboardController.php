<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\Pemeriksaan;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard utama pasien.
     */
    public function index()
    {
        $user = auth()->user();

        return view('pasien.dashboard', compact('user'));
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
