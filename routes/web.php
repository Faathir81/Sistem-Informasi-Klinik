<?php

use App\Http\Controllers\GajiSlipController;
use App\Http\Controllers\Midtrans\WebhookController;
use App\Http\Controllers\Pasien\AntreanController;
use App\Http\Controllers\Pasien\DashboardController as PasienDashboard;
use App\Http\Controllers\Pasien\PembayaranController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// ─────────────────────────────────────────────────────────────────────
// Landing Page
// ─────────────────────────────────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
})->name('home');

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

// ─────────────────────────────────────────────────────────────────────
// PROFILE ROUTES (dari Breeze)
// ─────────────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
