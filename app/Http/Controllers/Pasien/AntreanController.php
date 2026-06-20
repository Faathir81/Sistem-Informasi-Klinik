<?php

namespace App\Http\Controllers\Pasien;

use App\Enums\AntreanStatus;
use App\Http\Controllers\Controller;
use App\Models\Antrean;
use App\Models\Dokter;
use App\Models\Pasien;
use App\Services\Antrean\AntreanBookingService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AntreanController extends Controller
{
    /**
     * Tampilkan halaman form booking antrean.
     */
    public function create()
    {
        $user = Auth::user()->load('pasiens');
        $pasiens = $user->pasiens;

        if ($pasiens->isEmpty()) {
            return redirect()->route('pasien.dashboard')
                ->with('error', 'Belum ada profil pasien aktif. Tambahkan profil pasien terlebih dahulu.');
        }

        $selectedPasien = $pasiens->firstWhere('id', old('pasien_id')) ?? $pasiens->first();

        $antreanHariIni = Antrean::with(['pasien', 'dokter'])
            ->whereIn('pasien_id', $pasiens->pluck('id'))
            ->where('tanggal_kunjungan', today())
            ->whereIn('status', AntreanStatus::activeValues())
            ->first();

        $dokters = Dokter::where('status_aktif', true)->get();
        $hariIni = now()->locale('id')->isoFormat('dddd'); // Senin, Selasa, dst.

        return view('pasien.antrean.create', compact('pasiens', 'selectedPasien', 'dokters', 'hariIni', 'antreanHariIni'));
    }

    /**
     * Ambil jadwal tersedia berdasarkan dokter yang dipilih (AJAX).
     */
    public function getJadwal(Request $request, AntreanBookingService $booking)
    {
        $request->validate([
            'dokter_id' => ['required', 'integer', 'exists:dokters,id'],
            'tanggal' => ['nullable', 'date', 'after_or_equal:today'],
        ]);

        return response()->json($booking->scheduleAvailability(
            (int) $request->dokter_id,
            $request->tanggal ?? today()->toDateString(),
        ));
    }

    /**
     * Simpan booking antrean baru.
     */
    public function store(Request $request, AntreanBookingService $booking)
    {
        $data = $request->validate([
            'pasien_id' => ['required', 'exists:pasiens,id'],
            'dokter_id' => ['required', 'exists:dokters,id'],
            'jadwal_dokter_id' => ['required', 'exists:jadwal_dokters,id'],
            'tanggal_kunjungan' => ['required', 'date', 'after_or_equal:today'],
        ]);

        $user = Auth::user()->load('pasiens');
        $pasien = $user->pasiens->firstWhere('id', (int) $data['pasien_id']);

        if (! $pasien) {
            abort(403, 'Profil pasien tidak ditemukan.');
        }

        $antrean = $booking->create($pasien, $data);

        return redirect()->route('pasien.antrean.tiket', $antrean->kode_antrean)
            ->with('success', 'Antrean berhasil dibooking!');
    }

    /**
     * Tampilkan tiket antrean dengan QR Code.
     */
    public function tiket(string $kode)
    {
        $antrean = $this->authorizedTiket($kode);

        return view('pasien.antrean.tiket', compact('antrean'));
    }

    public function tiketPdf(string $kode)
    {
        $antrean = $this->authorizedTiket($kode);
        $filename = 'tiket-antrean-'.$antrean->kode_antrean.'.pdf';

        return Pdf::loadView('pasien.antrean.tiket-pdf', compact('antrean'))
            ->setPaper('a4', 'portrait')
            ->download($filename);
    }

    /**
     * Daftar antrean pasien saat ini.
     */
    public function index()
    {
        $user = Auth::user()->load('pasiens');
        $pasienIds = $user->pasiens->pluck('id');

        if ($pasienIds->isEmpty()) {
            return redirect()->route('pasien.dashboard')
                ->with('error', 'Belum ada profil pasien aktif. Tambahkan profil pasien terlebih dahulu.');
        }

        $antreans = Antrean::with(['pasien', 'dokter', 'jadwalDokter'])
            ->whereIn('pasien_id', $pasienIds)
            ->orderByDesc('tanggal_kunjungan')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('pasien.antrean.index', compact('antreans'));
    }

    private function authorizedTiket(string $kode): Antrean
    {
        $antrean = Antrean::with(['pasien', 'dokter', 'jadwalDokter'])
            ->where('kode_antrean', $kode)
            ->firstOrFail();

        $user = Auth::user()->load('pasiens');
        $pasienIds = $user->pasiens->pluck('id');

        if (! $pasienIds->contains($antrean->pasien_id)) {
            abort(403, 'Anda tidak berhak mengakses tiket ini.');
        }

        return $antrean;
    }

    /**
     * Batalkan antrean (hanya jika masih Menunggu).
     */
    public function batal(Antrean $antrean)
    {
        $user = Auth::user()->load('pasiens');
        $pasienIds = $user->pasiens->pluck('id');

        if ($pasienIds->isEmpty()) {
            abort(403, 'Profil pasien tidak ditemukan.');
        }

        if (! $pasienIds->contains($antrean->pasien_id)) {
            abort(403);
        }

        if ($antrean->status !== AntreanStatus::Menunggu->value) {
            return back()->with('error', 'Antrean tidak dapat dibatalkan karena statusnya sudah berubah.');
        }

        $antrean->update(['status' => AntreanStatus::Batal->value]);

        return back()->with('success', 'Antrean berhasil dibatalkan.');
    }
}
