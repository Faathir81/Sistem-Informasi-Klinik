<?php

use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\GajiSlipController;
use App\Http\Controllers\Midtrans\WebhookController;
use App\Http\Controllers\Pasien\AntreanController;
use App\Http\Controllers\Pasien\DashboardController as PasienDashboard;
use App\Http\Controllers\Pasien\PembayaranController;
use App\Http\Controllers\Pasien\PengajuanPasienController;
use App\Http\Controllers\ProfileController;
use App\Models\Antrean;
use Illuminate\Support\Facades\Route;

$landingQueuePreview = static function (): ?Antrean {
    return Antrean::query()
        ->with(['dokter', 'jadwalDokter'])
        ->whereDate('tanggal_kunjungan', today())
        ->whereIn('status', ['Dipanggil', 'Menunggu'])
        ->orderByRaw("CASE WHEN status = 'Dipanggil' THEN 0 WHEN status = 'Menunggu' THEN 1 ELSE 2 END")
        ->orderBy('nomor_antrean')
        ->first();
};

$maskQueueCode = static function (?string $code): string {
    if (! $code) {
        return 'Belum tersedia';
    }

    $prefix = substr($code, 0, 9);
    $suffix = substr($code, -2);

    return $prefix.'****'.$suffix;
};

// ─────────────────────────────────────────────────────────────────────
// Landing Page
// ─────────────────────────────────────────────────────────────────────
Route::get('/', function () use ($landingQueuePreview, $maskQueueCode) {
    return view('welcome', [
        'previewAntrean' => $landingQueuePreview(),
        'maskQueueCode' => $maskQueueCode,
    ]);
})->name('home');

Route::get('/antrean/live-preview', function () use ($landingQueuePreview, $maskQueueCode) {
    $antrean = $landingQueuePreview();

    if (! $antrean) {
        return response()->json([
            'active' => false,
            'number' => '--',
            'status' => 'Belum Ada',
            'doctor' => 'Belum ada antrean aktif',
            'schedule' => 'Booking antrean untuk hari ini',
            'code' => 'Belum tersedia',
            'updated_at' => now()->format('H:i:s'),
        ]);
    }

    return response()->json([
        'active' => true,
        'number' => str_pad((string) $antrean->nomor_antrean, 3, '0', STR_PAD_LEFT),
        'status' => $antrean->status,
        'doctor' => $antrean->dokter?->nama_dokter ?? 'Dokter belum tersedia',
        'schedule' => $antrean->jadwalDokter
            ? substr($antrean->jadwalDokter->jam_mulai, 0, 5).' - '.substr($antrean->jadwalDokter->jam_selesai, 0, 5).' WIB'
            : 'Jadwal belum tersedia',
        'code' => $maskQueueCode($antrean->kode_antrean),
        'updated_at' => now()->format('H:i:s'),
    ]);
})->name('antrean.live-preview');

// ─────────────────────────────────────────────────────────────────────
// DEFAULT DASHBOARD — redirect berdasarkan role setelah login
// ─────────────────────────────────────────────────────────────────────
Route::get('/dashboard', function () {
    if (auth()->user()->role === 'admin') {
        return redirect('/admin');
    }

    return redirect()->route('pasien.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ─────────────────────────────────────────────────────────────────────
// PASIEN ROUTES — hanya untuk pasien yang sudah login
// ─────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'is.pasien'])->prefix('pasien')->name('pasien.')->group(function () {
    Route::get('/dashboard', [PasienDashboard::class, 'index'])->name('dashboard');
    Route::get('/riwayat-medis', [PasienDashboard::class, 'riwayat'])->name('riwayat.index');
    Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
    Route::post('/pembayaran/{pemeriksaan}', [PembayaranController::class, 'store'])->name('pembayaran.store');
    Route::get('/pembayaran/transaksi/{transaksi}', [PembayaranController::class, 'show'])->name('pembayaran.show');
    Route::get('/pengajuan-pasien', [PengajuanPasienController::class, 'create'])->name('pengajuan-pasien.create');
    Route::post('/pengajuan-pasien', [PengajuanPasienController::class, 'store'])->name('pengajuan-pasien.store');

    // ── Antrean ──────────────────────────────────────────────────────────
    Route::get('/antrean', [AntreanController::class, 'index'])->name('antrean.index');
    Route::get('/antrean/booking', [AntreanController::class, 'create'])->name('antrean.create');
    Route::post('/antrean/booking', [AntreanController::class, 'store'])->name('antrean.store');
    Route::get('/antrean/jadwal', [AntreanController::class, 'getJadwal'])->name('antrean.jadwal');
    Route::get('/antrean/tiket/{kode}', [AntreanController::class, 'tiket'])->name('antrean.tiket');
    Route::patch('/antrean/{antrean}/batal', [AntreanController::class, 'batal'])->name('antrean.batal');
});

Route::post('/midtrans/webhook', WebhookController::class)->name('midtrans.webhook');

Route::middleware(['auth', 'is.admin'])->get('/admin/gaji/{gaji}/slip', GajiSlipController::class)->name('admin.gaji.slip');

Route::middleware(['auth', 'is.admin'])->prefix('admin/laporan')->name('admin.reports.')->group(function () {
    Route::get('/keuangan', [ReportController::class, 'keuangan'])->name('keuangan');
    Route::get('/kunjungan', [ReportController::class, 'kunjungan'])->name('kunjungan');
    Route::get('/stok-obat', [ReportController::class, 'stokObat'])->name('stok-obat');
});

// ─────────────────────────────────────────────────────────────────────
// PROFILE ROUTES (dari Breeze)
// ─────────────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
