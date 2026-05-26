<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="clinic-kicker">Portal pasien</p>
                <h1 class="mt-1 text-2xl font-black text-[#14342f]">Dashboard Pasien</h1>
            </div>
            <p class="text-sm font-semibold text-[#62756f]">{{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="clinic-section space-y-6">
            @if(session('success'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            @if(! $pasien)
                @php
                    $pengajuanStatusClass = match($pengajuanPasien?->status) {
                        'Menunggu' => 'clinic-badge-warning',
                        'Ditolak' => 'inline-flex items-center gap-2 rounded-md border border-red-200 bg-red-50 px-3 py-1 text-xs font-bold text-red-700',
                        default => 'clinic-badge-muted',
                    };
                @endphp
                <section class="clinic-card-solid overflow-hidden border-l-4 {{ $pengajuanPasien?->status === 'Ditolak' ? 'border-l-red-400' : 'border-l-[#ef7b2d]' }}">
                    <div class="grid gap-5 p-6 md:grid-cols-[1fr_auto] md:items-center">
                        <div>
                            <div class="flex flex-wrap items-center gap-3">
                                <p class="clinic-kicker">Verifikasi data pasien</p>
                                <span class="{{ $pengajuanStatusClass }}">
                                    {{ $pengajuanPasien?->status ?? 'Belum Mengajukan' }}
                                </span>
                            </div>
                            @if($pengajuanPasien?->status === 'Menunggu')
                                <h2 class="mt-2 text-2xl font-black text-[#14342f]">Pengajuan Anda sedang diverifikasi admin.</h2>
                                <p class="mt-2 max-w-3xl text-sm leading-6 text-[#62756f]">
                                    Setelah disetujui, sistem akan otomatis membuat nomor rekam medis dan fitur booking antrean akan aktif.
                                </p>
                            @elseif($pengajuanPasien?->status === 'Ditolak')
                                <h2 class="mt-2 text-2xl font-black text-[#14342f]">Pengajuan perlu diperbaiki.</h2>
                                <p class="mt-2 max-w-3xl text-sm leading-6 text-[#62756f]">
                                    Alasan admin: <span class="font-bold text-red-700">{{ $pengajuanPasien->alasan_penolakan ?: 'Belum ada alasan tertulis.' }}</span>
                                </p>
                            @else
                                <h2 class="mt-2 text-2xl font-black text-[#14342f]">Lengkapi data pasien untuk mengaktifkan fitur klinik.</h2>
                                <p class="mt-2 max-w-3xl text-sm leading-6 text-[#62756f]">
                                    Akun Anda sudah dibuat, tetapi belum terhubung dengan nomor rekam medis. Kirim pengajuan agar admin bisa memverifikasi data Anda.
                                </p>
                            @endif
                        </div>

                        @if($pengajuanPasien?->status !== 'Menunggu')
                            <a href="{{ route('pasien.pengajuan-pasien.create') }}" class="clinic-btn-primary">
                                {{ $pengajuanPasien?->status === 'Ditolak' ? 'Ajukan Ulang' : 'Ajukan Data Pasien' }}
                            </a>
                        @else
                            <span class="rounded-lg bg-[#f3faf6] px-4 py-3 text-sm font-bold text-[#62756f]">
                                Dikirim {{ $pengajuanPasien->created_at->format('d M Y H:i') }}
                            </span>
                        @endif
                    </div>
                </section>
            @endif

            <section class="grid gap-6 lg:grid-cols-[1.25fr_0.75fr]">
                <div class="clinic-card overflow-hidden">
                    <div class="grid min-h-[360px] gap-6 p-6 sm:p-8 lg:grid-cols-[1fr_0.72fr]">
                        <div class="flex flex-col justify-between gap-8">
                            <div>
                                <span class="inline-flex h-14 w-14 items-center justify-center rounded-md bg-[#14342f] text-2xl font-black text-white">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </span>
                                <p class="mt-6 clinic-kicker">Selamat datang</p>
                                <h2 class="mt-2 max-w-2xl text-4xl font-black leading-tight text-[#14342f]">
                                    {{ $user->name }}
                                </h2>
                                <p class="mt-4 max-w-2xl text-base leading-7 text-[#62756f]">
                                    Semua kebutuhan pasien ada di sini: booking antrean, tiket QR, riwayat medis, resep, dan pembayaran QRIS.
                                </p>
                            </div>

                            <div class="flex flex-col gap-3 sm:flex-row">
                                <a href="{{ $pasien ? route('pasien.antrean.create') : route('pasien.pengajuan-pasien.create') }}" id="btn-booking-antrean-card" class="clinic-btn-primary">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V4m8 3V4M5 11h14M6 20h12a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2Z"/>
                                    </svg>
                                    {{ $pasien ? 'Booking Antrean' : 'Ajukan Data Pasien' }}
                                </a>
                                <a href="{{ $pasien ? route('pasien.pembayaran.index') : route('pasien.pengajuan-pasien.create') }}" id="btn-pembayaran-qris" class="clinic-btn-secondary">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M6 11h12M7 15h5m-6 4h12a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2Z"/>
                                    </svg>
                                    {{ $pasien ? 'Pembayaran' : 'Status Verifikasi' }}
                                </a>
                            </div>
                        </div>

                        <div class="rounded-lg border border-[#d6e7dd] bg-[#f3faf6] p-5">
                            @if($antreanAktif)
                                @php
                                    $activeBadge = $antreanAktif->status === 'Dipanggil' ? 'clinic-badge-info' : 'clinic-badge-warning';
                                @endphp
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-[#62756f]">Antrean aktif</p>
                                        <p class="mt-2 text-6xl font-black leading-none text-[#14342f]">
                                            {{ str_pad($antreanAktif->nomor_antrean, 3, '0', STR_PAD_LEFT) }}
                                        </p>
                                    </div>
                                    <span class="{{ $activeBadge }}">
                                        <span class="h-2 w-2 rounded-full {{ $antreanAktif->status === 'Dipanggil' ? 'bg-sky-500' : 'bg-amber-500' }}"></span>
                                        {{ $antreanAktif->status }}
                                    </span>
                                </div>

                                <div class="mt-6 space-y-3 text-sm">
                                    <div class="rounded-lg bg-white p-4">
                                        <span class="text-xs font-bold text-[#62756f]">Dokter</span>
                                        <p class="mt-1 font-black text-[#14342f]">{{ $antreanAktif->dokter->nama_dokter }}</p>
                                        <p class="text-[#62756f]">{{ $antreanAktif->dokter->spesialisasi }}</p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="rounded-lg bg-white p-4">
                                            <span class="text-xs font-bold text-[#62756f]">Tanggal</span>
                                            <p class="mt-1 font-black text-[#14342f]">{{ $antreanAktif->tanggal_kunjungan->format('d M Y') }}</p>
                                        </div>
                                        <div class="rounded-lg bg-white p-4">
                                            <span class="text-xs font-bold text-[#62756f]">Jam</span>
                                            <p class="mt-1 font-black text-[#14342f]">
                                                {{ substr($antreanAktif->jadwalDokter->jam_mulai, 0, 5) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <a href="{{ route('pasien.antrean.tiket', $antreanAktif->kode_antrean) }}" class="mt-5 inline-flex w-full items-center justify-center rounded-md bg-[#14342f] px-4 py-3 text-sm font-bold text-white transition hover:bg-[#1f4b44]">
                                    Lihat Tiket QR
                                </a>
                            @else
                                <div class="flex h-full min-h-[280px] flex-col items-center justify-center text-center">
                                    <div class="clinic-icon-box h-14 w-14">
                                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V4m8 3V4M5 11h14M6 20h12a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2Z"/>
                                        </svg>
                                    </div>
                                    <h3 class="mt-5 text-xl font-black text-[#14342f]">Belum ada antrean aktif</h3>
                                    <p class="mt-2 max-w-xs text-sm leading-6 text-[#62756f]">Ambil jadwal kunjungan untuk mendapatkan nomor antrean dan QR Code.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <aside class="grid gap-4">
                    <div class="clinic-card-solid p-5">
                        <p class="clinic-kicker">Ringkasan</p>
                        <div class="mt-4 grid gap-3">
                            <div class="flex items-center justify-between rounded-lg bg-[#f3faf6] p-4">
                                <span class="text-sm font-bold text-[#62756f]">Total antrean</span>
                                <span class="text-2xl font-black text-[#14342f]">{{ $jumlahAntrean }}</span>
                            </div>
                            <div class="flex items-center justify-between rounded-lg bg-[#fff7ed] p-4">
                                <span class="text-sm font-bold text-[#62756f]">Riwayat medis</span>
                                <span class="text-2xl font-black text-[#14342f]">{{ $jumlahRiwayat }}</span>
                            </div>
                            <div class="flex items-center justify-between rounded-lg bg-sky-50 p-4">
                                <span class="text-sm font-bold text-[#62756f]">Tagihan aktif</span>
                                <span class="text-2xl font-black text-[#14342f]">{{ $tagihanBelumLunas }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="clinic-card-solid p-5">
                        <p class="clinic-kicker">Data akun</p>
                        <div class="mt-4 space-y-3 text-sm">
                            <div>
                                <span class="font-bold text-[#62756f]">Email</span>
                                <p class="mt-1 break-words font-semibold text-[#14342f]">{{ $user->email }}</p>
                            </div>
                            <div>
                                <span class="font-bold text-[#62756f]">Nomor HP</span>
                                <p class="mt-1 font-semibold text-[#14342f]">{{ $user->no_hp ?? '-' }}</p>
                            </div>
                            <div>
                                <span class="font-bold text-[#62756f]">No. Rekam Medis</span>
                                <p class="mt-1 font-mono text-sm font-semibold text-[#14342f]">{{ $pasien?->no_rekam_medis ?? 'Belum terhubung' }}</p>
                            </div>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="mt-5 inline-flex text-sm font-black text-[#ef7b2d] hover:text-[#c75f1d]" id="btn-edit-profile">
                            Edit profil
                        </a>
                    </div>
                </aside>
            </section>

            <section class="grid gap-4 md:grid-cols-3">
                @foreach ([
                    ['title' => 'Status Antrean', 'body' => 'Pantau semua riwayat antrean dan buka tiket QR.', 'route' => $pasien ? route('pasien.antrean.index') : route('pasien.pengajuan-pasien.create'), 'id' => 'btn-status-antrean-card'],
                    ['title' => 'Riwayat Medis', 'body' => 'Lihat diagnosa, tindakan, dan resep obat Anda.', 'route' => $pasien ? route('pasien.riwayat.index') : route('pasien.pengajuan-pasien.create'), 'id' => 'btn-riwayat-medis-card'],
                    ['title' => 'Pembayaran QRIS', 'body' => 'Buat transaksi pembayaran melalui Midtrans.', 'route' => $pasien ? route('pasien.pembayaran.index') : route('pasien.pengajuan-pasien.create'), 'id' => 'btn-pembayaran-card'],
                ] as $action)
                    <a href="{{ $action['route'] }}" id="{{ $action['id'] }}" class="clinic-card-solid clinic-hover-lift block p-6">
                        <h3 class="text-lg font-black text-[#14342f]">{{ $action['title'] }}</h3>
                        <p class="mt-2 min-h-12 text-sm leading-6 text-[#62756f]">{{ $action['body'] }}</p>
                        <span class="mt-5 inline-flex items-center gap-2 text-sm font-black text-[#ef7b2d]">
                            {{ $pasien ? 'Buka' : 'Lengkapi Data' }}
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7-7 7 7-7 7"/>
                            </svg>
                        </span>
                    </a>
                @endforeach
            </section>

            <section class="clinic-card-solid p-6">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="clinic-kicker">Pemeriksaan terakhir</p>
                        @if($pemeriksaanTerakhir)
                            <h3 class="mt-2 text-xl font-black text-[#14342f]">{{ $pemeriksaanTerakhir->dokter->nama_dokter }}</h3>
                            <p class="mt-1 text-sm leading-6 text-[#62756f]">
                                {{ $pemeriksaanTerakhir->tgl_pemeriksaan->format('d M Y') }} - {{ $pemeriksaanTerakhir->diagnosa }}
                            </p>
                        @else
                            <h3 class="mt-2 text-xl font-black text-[#14342f]">Belum ada pemeriksaan tercatat</h3>
                            <p class="mt-1 text-sm leading-6 text-[#62756f]">Riwayat medis akan muncul setelah admin menyelesaikan pemeriksaan.</p>
                        @endif
                    </div>
                    <a href="{{ $pasien ? route('pasien.riwayat.index') : route('pasien.pengajuan-pasien.create') }}" class="clinic-btn-secondary">
                        {{ $pasien ? 'Lihat Riwayat' : 'Lengkapi Data' }}
                    </a>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
