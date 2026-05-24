<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\Antrean;
use App\Models\Dokter;
use App\Models\JadwalDokter;
use App\Models\Pasien;
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

        if (!$pasien) {
            return redirect()->route('pasien.dashboard')
                ->with('error', 'Data pasien Anda belum terdaftar. Hubungi petugas klinik.');
        }

        // Cek jika pasien sudah punya antrean aktif hari ini
        $antreanHariIni = Antrean::where('pasien_id', $pasien->id)
            ->where('tanggal_kunjungan', today())
            ->whereIn('status', ['Menunggu', 'Dipanggil'])
            ->first();

        $dokters = Dokter::where('status_aktif', true)->get();
        $hariIni = now()->locale('id')->isoFormat('dddd'); // Senin, Selasa, dst.

        return view('pasien.antrean.create', compact('pasien', 'dokters', 'hariIni', 'antreanHariIni'));
    }

    /**
     * Ambil jadwal tersedia berdasarkan dokter yang dipilih (AJAX).
     */
    public function getJadwal(Request $request)
    {
        $dokterId = $request->dokter_id;
        $tanggal  = $request->tanggal ?? today()->toDateString();
        $hari     = now()->parse($tanggal)->locale('id')->isoFormat('dddd');

        $jadwals = JadwalDokter::with('dokter')
            ->where('dokter_id', $dokterId)
            ->where('hari', $hari)
            ->get()
            ->map(function ($j) use ($tanggal) {
                $terpakai = Antrean::where('jadwal_dokter_id', $j->id)
                    ->where('tanggal_kunjungan', $tanggal)
                    ->whereNotIn('status', ['Batal'])
                    ->count();
                $j->sisa_kuota = $j->kuota - $terpakai;
                return $j;
            });

        return response()->json($jadwals);
    }

    /**
     * Simpan booking antrean baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'dokter_id'        => 'required|exists:dokters,id',
            'jadwal_dokter_id' => 'required|exists:jadwal_dokters,id',
            'tanggal_kunjungan' => 'required|date|after_or_equal:today',
        ]);

        $user   = Auth::user();
        $pasien = Pasien::where('user_id', $user->id)->firstOrFail();

        // Pastikan kuota belum penuh
        $jadwal   = JadwalDokter::findOrFail($request->jadwal_dokter_id);
        $terpakai = Antrean::where('jadwal_dokter_id', $jadwal->id)
            ->where('tanggal_kunjungan', $request->tanggal_kunjungan)
            ->whereNotIn('status', ['Batal'])
            ->count();

        if ($terpakai >= $jadwal->kuota) {
            return back()->with('error', 'Maaf, kuota antrean untuk jadwal ini sudah penuh.');
        }

        // Cek duplikasi
        $sudahAda = Antrean::where('pasien_id', $pasien->id)
            ->where('dokter_id', $request->dokter_id)
            ->where('tanggal_kunjungan', $request->tanggal_kunjungan)
            ->whereNotIn('status', ['Batal'])
            ->exists();

        if ($sudahAda) {
            return back()->with('error', 'Anda sudah memiliki antrean untuk dokter ini pada tanggal tersebut.');
        }

        $antrean = Antrean::create([
            'pasien_id'         => $pasien->id,
            'dokter_id'         => $request->dokter_id,
            'jadwal_dokter_id'  => $request->jadwal_dokter_id,
            'tanggal_kunjungan' => $request->tanggal_kunjungan,
        ]);

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
        $user   = Auth::user();
        $pasien = Pasien::where('user_id', $user->id)->first();

        if (!$pasien || $antrean->pasien_id !== $pasien->id) {
            abort(403, 'Anda tidak berhak mengakses tiket ini.');
        }

        return view('pasien.antrean.tiket', compact('antrean'));
    }

    /**
     * Daftar antrean pasien saat ini.
     */
    public function index()
    {
        $user    = Auth::user();
        $pasien  = Pasien::where('user_id', $user->id)->first();

        if (!$pasien) {
            return redirect()->route('pasien.dashboard')
                ->with('error', 'Data pasien Anda belum terdaftar.');
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
        $user   = Auth::user();
        $pasien = Pasien::where('user_id', $user->id)->firstOrFail();

        if ($antrean->pasien_id !== $pasien->id) {
            abort(403);
        }

        if ($antrean->status !== 'Menunggu') {
            return back()->with('error', 'Antrean tidak dapat dibatalkan karena statusnya sudah berubah.');
        }

        $antrean->update(['status' => 'Batal']);

        return back()->with('success', 'Antrean berhasil dibatalkan.');
    }
}
