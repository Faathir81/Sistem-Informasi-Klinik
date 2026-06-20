<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Klinik Ar-Ridlo') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        @php
            $authUser = request()->user();
            $authPortalUrl = $authUser?->isAdmin() ? url('/admin') : route('pasien.antrean.create');
        @endphp

        <div class="min-h-screen bg-white text-[#14342f]">
            <header class="fixed inset-x-0 top-0 z-50">
                <div class="clinic-section pt-4">
                    <nav class="flex items-center justify-between rounded-lg border border-white/30 bg-white/80 px-4 py-3 shadow-[0_14px_38px_rgba(20,52,47,0.12)] backdrop-blur-xl">
                        <a href="{{ route('home') }}" class="flex items-center gap-3">
                            <span class="flex h-11 w-11 items-center justify-center rounded-md bg-[#14342f] text-white">
                                <x-application-logo class="h-8 w-8" />
                            </span>
                            <span>
                                <span class="block text-sm font-black leading-5">Klinik Ar-Ridlo</span>
                                <span class="block text-xs font-semibold text-[#62756f]">Sistem Informasi Klinik</span>
                            </span>
                        </a>

                        <div class="hidden items-center gap-1 md:flex">
                            <a href="#layanan" class="clinic-nav-link">Layanan</a>
                            <a href="#alur" class="clinic-nav-link">Alur Pasien</a>
                            <a href="#booking" class="clinic-nav-link">Antrean</a>
                            <a href="#faq" class="clinic-nav-link">FAQ</a>
                        </div>

                        <div class="flex items-center gap-2">
                            @auth
                                @if ($authUser?->isAdmin())
                                    <a href="/admin" class="clinic-btn-secondary min-h-10 px-4 py-2">Panel Admin</a>
                                @else
                                    <a href="{{ route('pasien.dashboard') }}" class="clinic-btn-primary min-h-10 px-4 py-2">Dashboard</a>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="clinic-btn-quiet hidden sm:inline-flex">Masuk</a>
                                <a href="{{ route('register') }}" class="clinic-btn-primary min-h-10 px-4 py-2">Daftar</a>
                            @endauth
                        </div>
                    </nav>
                </div>
            </header>

            <main>
                <section id="beranda" class="relative flex min-h-[78svh] items-end overflow-hidden bg-cover bg-center" style="background-image: url('{{ asset('images/klinik-hero.png') }}')">
                    <div class="absolute inset-0 bg-gradient-to-r from-[#14342f]/95 via-[#14342f]/70 to-[#14342f]/10"></div>
                    <div class="absolute inset-x-0 bottom-0 h-40 bg-gradient-to-t from-white to-transparent"></div>

                    <div class="clinic-section relative z-10 pb-14 pt-36 text-white sm:pb-16 lg:pb-20">
                        <div class="clinic-animate-in max-w-3xl">
                            <p class="clinic-kicker text-[#f8b37d]">Portal klinik digital</p>
                            <h1 class="mt-4 text-5xl font-black leading-none sm:text-6xl lg:text-7xl">Klinik Ar-Ridlo</h1>
                            <p class="mt-6 max-w-2xl text-lg leading-8 text-white/80">
                                Booking antrean online, tiket QR Code, riwayat pemeriksaan, resep obat, dan pembayaran QRIS dalam satu pengalaman pasien yang rapi.
                            </p>
                            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                                @auth
                                    <a href="{{ $authPortalUrl }}" class="clinic-btn-primary">
                                        Mulai Sekarang
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7-7 7 7-7 7"/>
                                        </svg>
                                    </a>
                                @else
                                    <a href="{{ route('register') }}" class="clinic-btn-primary">
                                        Daftar Akun Pasien
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7-7 7 7-7 7"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('login') }}" class="clinic-btn-secondary border-white/40 bg-white/10 text-white hover:bg-white/20">
                                        Masuk Portal
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </section>

                <section class="clinic-section -mt-8 relative z-20">
                    <div class="grid gap-3 rounded-lg border border-white bg-white p-3 shadow-[0_18px_48px_rgba(20,52,47,0.10)] md:grid-cols-3">
                        <div class="flex items-center gap-3 rounded-lg bg-[#f3faf6] p-4">
                            <span class="clinic-icon-box">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V4m8 3V4M5 11h14M6 20h12a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2Z"/>
                                </svg>
                            </span>
                            <div>
                                <p class="text-sm font-black">Antrean online</p>
                                <p class="text-xs font-semibold text-[#62756f]">Pilih dokter dan jadwal aktif.</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 rounded-lg bg-[#fff7ed] p-4">
                            <span class="clinic-icon-box border-orange-100 bg-orange-50 text-[#ef7b2d]">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v8m-4-4h8M7 4h10l2 4H5l2-4Zm-2 4h14v11a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V8Z"/>
                                </svg>
                            </span>
                            <div>
                                <p class="text-sm font-black">Resep terhubung</p>
                                <p class="text-xs font-semibold text-[#62756f]">Riwayat obat terbaca pasien.</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 rounded-lg bg-sky-50 p-4">
                            <span class="clinic-icon-box border-sky-100 bg-sky-50 text-sky-700">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M6 11h12M7 15h5m-6 4h12a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2Z"/>
                                </svg>
                            </span>
                            <div>
                                <p class="text-sm font-black">Pembayaran QRIS</p>
                                <p class="text-xs font-semibold text-[#62756f]">Tagihan dan pembayaran online.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="layanan" class="clinic-section py-20">
                    <div class="grid gap-10 lg:grid-cols-[0.82fr_1.18fr] lg:items-end">
                        <div>
                            <p class="clinic-kicker">Layanan utama</p>
                            <h2 class="clinic-heading mt-3">Sesuai alur operasional klinik, tanpa menu yang tidak didukung sistem.</h2>
                        </div>
                        <p class="clinic-subcopy">
                            Setiap tampilan dirancang mengikuti database dan use case aplikasi: pasien mendaftar, mengambil antrean, diperiksa dokter, menerima resep, lalu menyelesaikan pembayaran.
                        </p>
                    </div>

                    <div class="mt-10 grid gap-5 md:grid-cols-3">
                        @foreach ([
                            ['title' => 'Konsultasi Medis', 'body' => 'Pemeriksaan oleh dokter terdaftar, lengkap dengan diagnosa, tindakan, dan biaya konsultasi.'],
                            ['title' => 'Farmasi & Resep', 'body' => 'Resep obat terhubung ke stok apotek dan dapat dilihat kembali oleh pasien.'],
                            ['title' => 'Antrean Digital', 'body' => 'Pasien menerima nomor antrean dan QR Code untuk ditunjukkan di klinik.'],
                        ] as $service)
                            <article class="clinic-card-solid clinic-hover-lift p-6">
                                <div class="mb-5 h-1.5 w-16 rounded-md bg-[#ef7b2d]"></div>
                                <h3 class="text-xl font-black text-[#14342f]">{{ $service['title'] }}</h3>
                                <p class="mt-3 text-sm leading-7 text-[#62756f]">{{ $service['body'] }}</p>
                            </article>
                        @endforeach
                    </div>
                </section>

                <section id="alur" class="bg-[#f3faf6] py-20">
                    <div class="clinic-section">
                        <div class="grid gap-6 md:grid-cols-2 md:items-start lg:gap-10">
                            <div>
                                <p class="clinic-kicker">Alur pasien</p>
                                <h2 class="clinic-heading mt-3">Dari booking sampai pembayaran dibuat ringkas.</h2>
                                <div class="mt-8 grid gap-4">
                                    @foreach ([
                                        ['step' => '01', 'title' => 'Buat akun pasien', 'body' => 'Data akun dipakai untuk mengamankan antrean, riwayat, dan transaksi.'],
                                        ['step' => '02', 'title' => 'Ambil nomor antrean', 'body' => 'Pilih tanggal, dokter, dan jadwal yang masih memiliki kuota.'],
                                        ['step' => '03', 'title' => 'Tunjukkan tiket QR', 'body' => 'QR Code antrean menjadi bukti kunjungan saat pasien tiba.'],
                                        ['step' => '04', 'title' => 'Bayar tagihan QRIS', 'body' => 'Setelah pemeriksaan dan resep dicatat, pasien dapat membuat pembayaran QRIS.'],
                                    ] as $item)
                                        <div class="flex items-start gap-4 rounded-lg border border-white bg-white p-4 shadow-sm">
                                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-md bg-[#14342f] text-sm font-black text-white">{{ $item['step'] }}</span>
                                            <div>
                                                <h3 class="font-black text-[#14342f]">{{ $item['title'] }}</h3>
                                                <p class="mt-1 text-sm leading-6 text-[#62756f]">{{ $item['body'] }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            @php
                                $previewNumber = $previewAntrean ? str_pad((string) $previewAntrean->nomor_antrean, 3, '0', STR_PAD_LEFT) : '--';
                                $previewStatus = $previewAntrean?->status ?? 'Belum Ada';
                                $previewDoctor = $previewAntrean?->dokter?->nama_dokter ?? 'Belum ada antrean aktif';
                                $previewSchedule = $previewAntrean?->jadwalDokter
                                    ? substr($previewAntrean->jadwalDokter->jam_mulai, 0, 5).' - '.substr($previewAntrean->jadwalDokter->jam_selesai, 0, 5).' WIB'
                                    : 'Booking antrean untuk hari ini';
                                $previewCode = $previewAntrean ? $maskQueueCode($previewAntrean->kode_antrean) : 'Belum tersedia';
                            @endphp

                            <div class="clinic-card-solid overflow-hidden" style="align-self: end;" data-queue-preview-url="{{ route('antrean.live-preview') }}">
                                <div class="border-b border-slate-100 bg-white p-5">
                                    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                        <div>
                                            <p class="clinic-kicker">Live antrean</p>
                                            <h3 class="mt-2 text-2xl font-black">Monitor antrean hari ini</h3>
                                        </div>
                                        <span class="rounded-md bg-[#f3faf6] px-3 py-1 text-xs font-bold text-[#62756f]">
                                            Update <span id="queue-preview-updated">{{ now()->format('H:i:s') }}</span>
                                        </span>
                                    </div>
                                </div>
                                <div class="p-5">
                                    <div class="rounded-lg border border-[#d6e7dd] bg-[#f7fbf7] p-5">
                                        <div class="flex items-start justify-between gap-4">
                                            <div>
                                                <p class="text-xs font-bold uppercase tracking-[0.16em] text-[#62756f]">Nomor antrean</p>
                                                <p id="queue-preview-number" class="mt-2 text-6xl font-black text-[#14342f]">{{ $previewNumber }}</p>
                                            </div>
                                            <x-status-badge id="queue-preview-badge" type="antrean" :value="$previewStatus" dot-id="queue-preview-dot">
                                                <span id="queue-preview-status">{{ $previewStatus }}</span>
                                            </x-status-badge>
                                        </div>
                                        <div class="mt-5 grid gap-3 text-sm sm:grid-cols-2">
                                            <div class="rounded-lg bg-white p-3">
                                                <span class="text-xs font-bold text-[#62756f]">Dokter</span>
                                                <p id="queue-preview-doctor" class="mt-1 font-black">{{ $previewDoctor }}</p>
                                            </div>
                                            <div class="rounded-lg bg-white p-3">
                                                <span class="text-xs font-bold text-[#62756f]">Jadwal</span>
                                                <p id="queue-preview-schedule" class="mt-1 font-black">{{ $previewSchedule }}</p>
                                            </div>
                                        </div>
                                        <div class="mt-4 flex items-center justify-between rounded-lg bg-white p-4">
                                            <div class="grid h-20 w-20 grid-cols-4 gap-1 rounded-md border border-slate-200 bg-white p-2">
                                                @for ($i = 0; $i < 16; $i++)
                                                    <span class="{{ in_array($i, [0, 2, 3, 5, 6, 8, 11, 12, 14]) ? 'bg-[#14342f]' : 'bg-[#dff3e8]' }} rounded-sm"></span>
                                                @endfor
                                            </div>
                                            <div class="text-right">
                                                <p class="text-xs font-bold uppercase tracking-[0.16em] text-[#62756f]">Kode</p>
                                                <p id="queue-preview-code" class="mt-1 font-mono text-sm font-black">{{ $previewCode }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="booking" class="clinic-section py-20">
                    <div class="grid gap-8 rounded-lg border border-[#d6e7dd] bg-[#14342f] p-6 text-white shadow-[0_24px_65px_rgba(20,52,47,0.20)] md:grid-cols-[1fr_auto] md:items-center md:p-8">
                        <div>
                            <p class="clinic-kicker text-[#f8b37d]">Siap ambil antrean</p>
                            <h2 class="mt-3 text-3xl font-black leading-tight sm:text-4xl">Masuk ke portal pasien dan pilih jadwal kunjungan.</h2>
                            <p class="mt-4 max-w-2xl text-sm leading-7 text-white/75">
                                Pasien yang sudah memiliki akun bisa langsung booking. Pasien baru perlu membuat akun agar antrean, riwayat medis, dan transaksi tetap aman.
                            </p>
                        </div>
                        <div class="flex flex-col gap-3 sm:flex-row md:flex-col">
                            @auth
                                <a href="{{ $authPortalUrl }}" class="clinic-btn-primary whitespace-nowrap">Buka Portal</a>
                            @else
                                <a href="{{ route('register') }}" class="clinic-btn-primary whitespace-nowrap">Daftar Akun</a>
                                <a href="{{ route('login') }}" class="clinic-btn-secondary whitespace-nowrap border-white/30 bg-white/10 text-white hover:bg-white/20">Masuk</a>
                            @endauth
                        </div>
                    </div>
                </section>

                <section id="faq" class="border-t border-slate-100 bg-white py-20">
                    <div class="clinic-section">
                        <div class="mx-auto max-w-3xl text-center">
                            <p class="clinic-kicker">FAQ</p>
                            <h2 class="clinic-heading mt-3">Pertanyaan yang relevan dengan sistem.</h2>
                        </div>
                        <div class="mx-auto mt-10 grid max-w-4xl gap-4">
                            @foreach ([
                                ['q' => 'Bagaimana cara mendaftar antrean online?', 'a' => 'Buat akun pasien, masuk ke dashboard, pilih tanggal kunjungan dan dokter, lalu sistem membuat nomor antrean beserta QR Code.'],
                                ['q' => 'Apakah tiket antrean bisa dicetak?', 'a' => 'Bisa. Tiket antrean menampilkan nomor, jadwal dokter, kode antrean, dan QR Code yang dapat dicetak atau disimpan sebagai PDF.'],
                                ['q' => 'Kapan pasien bisa membayar QRIS?', 'a' => 'Pembayaran dibuat setelah admin mencatat pemeriksaan dan resep. Pasien menentukan biaya konsultasi, lalu sistem menjumlahkannya dengan total resep obat.'],
                            ] as $index => $faq)
                                <div x-data="{ open: false }" class="clinic-card-solid p-5">
                                    <button
                                        type="button"
                                        @click="open = ! open"
                                        :aria-expanded="open.toString()"
                                        aria-controls="faq-answer-{{ $index }}"
                                        class="flex w-full items-center justify-between gap-4 rounded-md text-left font-black text-[#14342f] focus:outline-none"
                                    >
                                        <span>{{ $faq['q'] }}</span>
                                        <span
                                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-md bg-[#eef8f2] text-[#386258] transition duration-300"
                                            :style="open ? 'transform: rotate(45deg)' : 'transform: rotate(0deg)'"
                                        >
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"/>
                                            </svg>
                                        </span>
                                    </button>
                                    <div
                                        id="faq-answer-{{ $index }}"
                                        role="region"
                                        :aria-hidden="(! open).toString()"
                                        :style="`display: grid; grid-template-rows: ${open ? '1fr' : '0fr'}; opacity: ${open ? '1' : '0'}; transition: grid-template-rows 320ms cubic-bezier(0.16, 1, 0.3, 1), opacity 220ms ease;`"
                                    >
                                        <div class="overflow-hidden">
                                            <p class="pt-4 text-sm leading-7 text-[#62756f]">{{ $faq['a'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>
            </main>

            <footer class="border-t border-slate-100 bg-[#f7fbf7] py-8">
                <div class="clinic-section flex flex-col justify-between gap-4 text-sm font-semibold text-[#62756f] sm:flex-row">
                    <p>&copy; {{ date('Y') }} Klinik Ar-Ridlo. Sistem informasi klinik.</p>
                </div>
            </footer>
        </div>

    </body>
</html>
