<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Pasien') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Welcome Card --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-2xl">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">Selamat Datang, {{ auth()->user()->name }}! 👋</h3>
                            <p class="text-sm text-gray-500">Kelola antrean, riwayat pengobatan, dan pembayaran Anda di sini.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Action Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- Ambil Antrean --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-emerald-500 hover:-translate-y-1 transition-transform duration-300">
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <h4 class="font-semibold text-gray-800">Booking Antrean</h4>
                        </div>
                        <p class="text-sm text-gray-500 mb-4">Ambil nomor antrean dan dapatkan QR Code digital Anda.</p>
                        <a href="{{ route('pasien.antrean.create') }}" id="btn-booking-antrean-card"
                           class="inline-flex items-center gap-1 text-xs font-medium text-white bg-emerald-600 hover:bg-emerald-700 px-4 py-1.5 rounded-full transition">
                            📅 Booking Sekarang
                        </a>
                    </div>
                </div>

                {{-- Status Antrean --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-blue-400 hover:-translate-y-1 transition-transform duration-300">
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <h4 class="font-semibold text-gray-800">Status Antrean</h4>
                        </div>
                        <p class="text-sm text-gray-500 mb-4">Pantau posisi antrean dan status panggilan dokter Anda.</p>
                        <a href="{{ route('pasien.antrean.index') }}" id="btn-status-antrean-card"
                           class="inline-flex items-center gap-1 text-xs font-medium text-white bg-blue-500 hover:bg-blue-600 px-4 py-1.5 rounded-full transition">
                            📋 Lihat Antrean Saya
                        </a>
                    </div>
                </div>

                {{-- Riwayat Medis --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-orange-400 hover:-translate-y-1 transition-transform duration-300">
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-lg bg-orange-50 flex items-center justify-center">
                                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <h4 class="font-semibold text-gray-800">Riwayat Medis</h4>
                        </div>
                        <p class="text-sm text-gray-500 mb-4">Lihat riwayat pemeriksaan dan resep obat Anda.</p>
                        <span class="text-xs font-medium text-orange-600 bg-orange-50 px-3 py-1 rounded-full">🔜 Segera Hadir</span>
                    </div>
                </div>

            </div>

            {{-- Info Akun --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Informasi Akun Saya</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="text-gray-400">Nama Lengkap</span>
                            <p class="font-medium text-gray-800 mt-1">{{ auth()->user()->name }}</p>
                        </div>
                        <div>
                            <span class="text-gray-400">Alamat Email</span>
                            <p class="font-medium text-gray-800 mt-1">{{ auth()->user()->email }}</p>
                        </div>
                        <div>
                            <span class="text-gray-400">Nomor HP</span>
                            <p class="font-medium text-gray-800 mt-1">{{ auth()->user()->no_hp ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <a href="{{ route('profile.edit') }}" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium" id="btn-edit-profile">
                            ✏️ Edit Profil & Ganti Kata Sandi →
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
