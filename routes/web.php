<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Pasien\DashboardController as PasienDashboard;
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
