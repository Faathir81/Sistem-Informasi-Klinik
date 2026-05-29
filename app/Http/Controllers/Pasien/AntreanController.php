<?php

namespace App\Http\Controllers\Pasien;

use App\Enums\AntreanStatus;
use App\Http\Controllers\Controller;
use App\Models\Antrean;
use App\Models\Dokter;
use App\Models\Pasien;
use App\Services\Antrean\AntreanBookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AntreanController extends Controller
{
    /**
     * Tampilkan halaman form booking antrean.
     */
    public function create()
    {
        $user = Auth::user();
        $pasien = Pasien::where('user_id', $user->id)->first();

        if (! $pasien) {
            return redirect()->route('pasien.dashboard')
                ->with('error', 'Data pasien Anda belum aktif. Ajukan data pasien terlebih dahulu agar admin dapat membuat nomor rekam medis.');
        }

        // Cek jika pasien sudah punya antrean aktif hari ini
        $antreanHariIni = Antrean::where('pasien_id', $pasien->id)
            ->where('tanggal_kunjungan', today())
            ->whereIn('status', AntreanStatus::activeValues())
            ->first();

        $dokters = Dokter::where('status_aktif', true)->get();
        $hariIni = now()->locale('id')->isoFormat('dddd'); // Senin, Selasa, dst.

        return view('pasien.antrean.create', compact('pasien', 'dokters', 'hariIni', 'antreanHariIni'));
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

        return response()->json($booking->availableSchedules(
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
            'dokter_id' => ['required', 'exists:dokters,id'],
            'jadwal_dokter_id' => ['required', 'exists:jadwal_dokters,id'],
            'tanggal_kunjungan' => ['required', 'date', 'after_or_equal:today'],
        ]);

        $user = Auth::user();
        $pasien = Pasien::where('user_id', $user->id)->firstOrFail();

        $antrean = $booking->create($pasien, $data);

        return redirect()->route('pasien.antrean.tiket', $antrean->kode_antrean)
            ->with('success', 'Antrean berhasil dibooking!');
    }

    /**
     * Tampilkan tiket antrean dengan QR Code.
     */
    public function tiket(string $kode)
    {
        $antrean = Antrean::with(['pasien', 'dokter', 'jadwalDokter'])
            ->where('kode_antrean', $kode)
            ->firstOrFail();

        // Pastikan hanya pasien yang bersangkutan bisa melihat tiketnya
        $user = Auth::user();
        $pasien = Pasien::where('user_id', $user->id)->first();

        if (! $pasien || $antrean->pasien_id !== $pasien->id) {
            abort(403, 'Anda tidak berhak mengakses tiket ini.');
        }

        return view('pasien.antrean.tiket', compact('antrean'));
    }

    /**
     * Daftar antrean pasien saat ini.
     */
    public function index()
    {
        $user = Auth::user();
        $pasien = Pasien::where('user_id', $user->id)->first();

        if (! $pasien) {
            return redirect()->route('pasien.dashboard')
                ->with('error', 'Data pasien Anda belum aktif. Ajukan data pasien terlebih dahulu agar admin dapat membuat nomor rekam medis.');
        }

        $antreans = Antrean::with(['dokter', 'jadwalDokter'])
            ->where('pasien_id', $pasien->id)
            ->orderByDesc('tanggal_kunjungan')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('pasien.antrean.index', compact('antreans', 'pasien'));
    }

    /**
     * Batalkan antrean (hanya jika masih Menunggu).
     */
    public function batal(Antrean $antrean)
    {
        $user = Auth::user();
        $pasien = Pasien::where('user_id', $user->id)->firstOrFail();

        if ($antrean->pasien_id !== $pasien->id) {
            abort(403);
        }

        if ($antrean->status !== AntreanStatus::Menunggu->value) {
            return back()->with('error', 'Antrean tidak dapat dibatalkan karena statusnya sudah berubah.');
        }

        $antrean->update(['status' => AntreanStatus::Batal->value]);

        return back()->with('success', 'Antrean berhasil dibatalkan.');
    }
}
