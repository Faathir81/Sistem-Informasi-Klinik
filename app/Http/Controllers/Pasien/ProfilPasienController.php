<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePasienProfileRequest;
use App\Models\Pasien;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProfilPasienController extends Controller
{
    public function index(): View
    {
        $pasiens = auth()->user()
            ->pasiens()
            ->withCount(['antreans', 'pemeriksaans'])
            ->orderBy('nama_pasien')
            ->get();

        return view('pasien.profil.index', compact('pasiens'));
    }

    public function edit(Pasien $pasien): View
    {
        $this->authorizeOwner($pasien);

        return view('pasien.profil.edit', compact('pasien'));
    }

    public function update(UpdatePasienProfileRequest $request, Pasien $pasien): RedirectResponse
    {
        $pasien->update($request->validated());

        return redirect()
            ->route('pasien.profil.index')
            ->with('success', 'Profil pasien berhasil diperbarui.');
    }

    public function destroy(Pasien $pasien): RedirectResponse
    {
        $this->authorizeOwner($pasien);

        if ($pasien->antreans()->exists() || $pasien->pemeriksaans()->exists()) {
            return back()->with('error', 'Profil pasien tidak dapat dihapus karena sudah memiliki antrean atau riwayat pemeriksaan.');
        }

        $pasien->delete();

        return redirect()
            ->route('pasien.profil.index')
            ->with('success', 'Profil pasien berhasil dihapus.');
    }

    private function authorizeOwner(Pasien $pasien): void
    {
        abort_unless((int) $pasien->user_id === (int) auth()->id(), 403);
    }
}
