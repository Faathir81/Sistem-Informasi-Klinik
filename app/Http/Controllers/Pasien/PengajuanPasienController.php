<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\PengajuanPasien;
use App\Models\Pasien;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PengajuanPasienController extends Controller
{
    public function create(): View|RedirectResponse
    {
        $user = auth()->user()->load('latestPengajuanPasien', 'pasien');

        if ($user->pasien) {
            return redirect()
                ->route('pasien.dashboard')
                ->with('success', 'Data pasien Anda sudah terdaftar.');
        }

        $pengajuan = $user->latestPengajuanPasien;

        if ($pengajuan?->status === 'Menunggu') {
            return redirect()
                ->route('pasien.dashboard')
                ->with('success', 'Pengajuan data pasien Anda sedang menunggu verifikasi admin.');
        }

        return view('pasien.pengajuan.create', compact('user', 'pengajuan'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user()->load('pasien');

        if ($user->pasien) {
            return redirect()
                ->route('pasien.dashboard')
                ->with('success', 'Data pasien Anda sudah terdaftar.');
        }

        $pendingPengajuan = PengajuanPasien::where('user_id', $user->id)
            ->where('status', 'Menunggu')
            ->exists();

        if ($pendingPengajuan) {
            return redirect()
                ->route('pasien.dashboard')
                ->with('success', 'Pengajuan data pasien Anda sedang menunggu verifikasi admin.');
        }

        $data = $request->validate([
            'nik' => [
                'required',
                'digits:16',
                Rule::unique(Pasien::class, 'nik'),
            ],
            'nama_pasien' => ['required', 'string', 'max:255'],
            'tgl_lahir' => ['required', 'date', 'before:today'],
            'jenis_kelamin' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
            'alamat' => ['required', 'string', 'max:1000'],
            'no_hp' => ['required', 'string', 'max:20'],
            'catatan_pasien' => ['nullable', 'string', 'max:1000'],
        ]);

        $nikSedangDiajukan = PengajuanPasien::where('nik', $data['nik'])
            ->whereIn('status', ['Menunggu', 'Disetujui'])
            ->exists();

        if ($nikSedangDiajukan) {
            return back()
                ->withInput()
                ->withErrors(['nik' => 'NIK ini sedang diajukan atau sudah disetujui.']);
        }

        PengajuanPasien::create([
            ...$data,
            'user_id' => $user->id,
            'status' => 'Menunggu',
        ]);

        return redirect()
            ->route('pasien.dashboard')
            ->with('success', 'Pengajuan data pasien berhasil dikirim. Admin akan memverifikasi data Anda.');
    }
}
