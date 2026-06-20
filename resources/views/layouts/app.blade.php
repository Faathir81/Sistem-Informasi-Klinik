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
    <body class="font-sans antialiased">
        <div class="clinic-page">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header @class([
                    'border-b border-white/70 backdrop-blur',
                    'bg-white/70' => ! request()->routeIs('pasien.dashboard'),
                    'patient-dashboard-page-header' => request()->routeIs('pasien.dashboard'),
                ])>
                    <div class="clinic-section py-6">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="relative">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
