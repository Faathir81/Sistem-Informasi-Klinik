<?php

use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\GajiSlipController;
use App\Http\Controllers\Midtrans\WebhookController;
use App\Http\Controllers\Pasien\AntreanController;
use App\Http\Controllers\Pasien\DashboardController as PasienDashboard;
use App\Http\Controllers\Pasien\PembayaranController;
use App\Http\Controllers\Pasien\PengajuanPasienController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\LiveQueuePreviewController;
use App\Services\Antrean\LiveQueuePreviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ─────────────────────────────────────────────────────────────────────
// Landing Page
// ─────────────────────────────────────────────────────────────────────
Route::get('/', function (LiveQueuePreviewService $preview) {
    return view('welcome', [
        'previewAntrean' => $preview->current(),
        'maskQueueCode' => fn (?string $code): string => $preview->maskQueueCode($code),
    ]);
})->name('home');

Route::get('/antrean/live-preview', LiveQueuePreviewController::class)->name('antrean.live-preview');

Route::redirect('/admin/login', '/login')->name('admin.login.redirect');

// ─────────────────────────────────────────────────────────────────────
// DEFAULT DASHBOARD — redirect berdasarkan role setelah login
// ─────────────────────────────────────────────────────────────────────
Route::get('/dashboard', function (Request $request) {
    if ($request->user()?->role === 'admin') {
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
    Route::get('/antrean/tiket/{kode}/pdf', [AntreanController::class, 'tiketPdf'])->name('antrean.tiket.pdf');
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
