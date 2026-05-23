<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsPasien
{
    /**
     * Handle an incoming request.
     * Hanya mengizinkan akses jika user sudah login dan memiliki role 'pasien'.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || auth()->user()->role !== 'pasien') {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu dengan akun pasien Anda.');
        }

        return $next($request);
    }
}
