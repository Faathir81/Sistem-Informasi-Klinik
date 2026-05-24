<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Pasien\DashboardController as PasienDashboard;
use App\Http\Controllers\Pasien\AntreanController;
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

    // ── Antrean ──────────────────────────────────────────────────────────
    Route::get('/antrean',                   [AntreanController::class, 'index'])->name('antrean.index');
    Route::get('/antrean/booking',           [AntreanController::class, 'create'])->name('antrean.create');
    Route::post('/antrean/booking',          [AntreanController::class, 'store'])->name('antrean.store');
    Route::get('/antrean/jadwal',            [AntreanController::class, 'getJadwal'])->name('antrean.jadwal');
    Route::get('/antrean/tiket/{kode}',      [AntreanController::class, 'tiket'])->name('antrean.tiket');
    Route::patch('/antrean/{antrean}/batal', [AntreanController::class, 'batal'])->name('antrean.batal');
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
