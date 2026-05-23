<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard utama pasien.
     */
    public function index()
    {
        $user = auth()->user();
        return view('pasien.dashboard', compact('user'));
    }
}
