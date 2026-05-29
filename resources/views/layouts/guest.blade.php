<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Klinik Ar-Ridlo') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-[#14342f] antialiased">
        <div class="min-h-screen bg-[#f7fbf7]">
            <div class="clinic-section grid min-h-screen items-center gap-10 py-8 lg:grid-cols-[0.95fr_1.05fr]">
                <section class="hidden min-h-[620px] overflow-hidden rounded-lg bg-cover bg-center shadow-[0_28px_70px_rgba(20,52,47,0.18)] lg:block" style="background-image: url('{{ asset('images/klinik-hero.png') }}')">
                    <div class="flex h-full flex-col justify-between bg-gradient-to-br from-[#14342f]/80 via-[#14342f]/60 to-transparent p-10 text-white">
                        <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                            <span class="flex h-12 w-12 items-center justify-center rounded-md bg-white text-[#14342f]">
                                <x-application-logo class="h-8 w-8" />
                            </span>
                            <span class="text-lg font-black">Klinik Ar-Ridlo</span>
                        </a>

                        <div class="max-w-md space-y-5">
                            <p class="clinic-kicker text-[#f8b37d]">Portal pasien</p>
                            <h1 class="text-4xl font-black leading-tight">Antrean digital, riwayat medis, dan pembayaran QRIS dalam satu alur.</h1>
                            <div class="grid grid-cols-3 gap-3 text-sm">
                                <div class="rounded-lg border border-white/20 bg-white/10 p-3 backdrop-blur">
                                    <span class="block text-2xl font-black">QR</span>
                                    <span class="text-white/75">Tiket antrean</span>
                                </div>
                                <div class="rounded-lg border border-white/20 bg-white/10 p-3 backdrop-blur">
                                    <span class="block text-2xl font-black">24/7</span>
                                    <span class="text-white/75">Akses portal</span>
                                </div>
                                <div class="rounded-lg border border-white/20 bg-white/10 p-3 backdrop-blur">
                                    <span class="block text-2xl font-black">QRIS</span>
                                    <span class="text-white/75">Pembayaran</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="mx-auto w-full max-w-md">
                    <div class="mb-7 flex items-center justify-between gap-4">
                        <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                            <span class="flex h-12 w-12 items-center justify-center rounded-md bg-[#14342f] text-white shadow-sm">
                                <x-application-logo class="h-8 w-8" />
                            </span>
                            <span>
                                <span class="block text-base font-black">Klinik Ar-Ridlo</span>
                                <span class="block text-xs font-semibold text-[#62756f]">Sistem Informasi Klinik</span>
                            </span>
                        </a>
                    </div>

                    <div class="clinic-card-solid p-6 sm:p-7">
                        {{ $slot }}
                    </div>
                </section>
            </div>
        </div>
    </body>
</html>
