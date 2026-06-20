<x-app-layout>
    <style>
        .patient-dashboard-grid,
        .patient-dashboard-main,
        .patient-dashboard-sidebar,
        .patient-dashboard-hero-grid {
            display: grid;
            gap: 1.25rem;
        }

        .patient-dashboard-main {
            min-width: 0;
        }

        .patient-dashboard-summary-grid,
        .patient-dashboard-account-grid,
        .patient-dashboard-account-actions {
            display: grid;
            gap: 0.75rem;
        }

        @media (min-width: 640px) and (max-width: 1023px) {
            .patient-dashboard-summary-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .patient-dashboard-account-grid,
            .patient-dashboard-account-actions {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (min-width: 1024px) {
            .patient-dashboard-page-header {
                position: sticky;
                top: 72px;
                z-index: 30;
                background-color: rgba(247, 251, 247, 0.86);
                -webkit-backdrop-filter: blur(28px) saturate(120%);
                backdrop-filter: blur(28px) saturate(120%);
                box-shadow: 0 10px 28px rgba(20, 52, 47, 0.06);
            }

            .patient-dashboard-grid {
                grid-template-columns: minmax(0, 1fr) 340px;
                align-items: start;
            }

            .patient-dashboard-sidebar {
                position: sticky;
                top: 189px;
                align-self: start;
                max-height: calc(100vh - 209px);
                overflow-y: auto;
                overscroll-behavior: contain;
                padding-right: 0.25rem;
                scrollbar-gutter: stable;
                scrollbar-width: thin;
                scrollbar-color: #b8cec3 transparent;
            }

            .patient-dashboard-sidebar::-webkit-scrollbar {
                width: 6px;
            }

            .patient-dashboard-sidebar::-webkit-scrollbar-thumb {
                border-radius: 999px;
                background: #b8cec3;
            }
        }

        @media (min-width: 1280px) {
            .patient-dashboard-hero-grid {
                grid-template-columns: minmax(0, 1fr) 300px;
                align-items: stretch;
            }
        }
    </style>

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

            @if($pasiens->isEmpty())
                <section class="clinic-surface overflow-hidden border-l-4 {{ $pengajuanPasien?->isPembayaranGagal() ? 'border-l-red-400' : 'border-l-[#ef7b2d]' }}">
                    <div class="grid gap-5 p-5 sm:p-6 md:grid-cols-[1fr_auto] md:items-center">
                        <div>
                            <div class="flex flex-wrap items-center gap-3">
                                <p class="clinic-kicker">Verifikasi profil pasien</p>
                                <x-status-badge type="pengajuan" :value="$pengajuanPasien?->status" />
                            </div>
                            @if($pengajuanPasien?->isMenungguPembayaran())
                                <h2 class="mt-2 text-xl font-black leading-tight text-[#14342f] sm:text-2xl">Pengajuan Anda menunggu pembayaran pendaftaran.</h2>
                                <p class="mt-2 max-w-3xl text-sm leading-6 text-[#62756f]">
                                    Selesaikan pembayaran Rp1.000 agar profil pasien aktif dan fitur booking antrean bisa digunakan.
                                </p>
                            @elseif($pengajuanPasien?->isPembayaranGagal())
                                <h2 class="mt-2 text-xl font-black leading-tight text-[#14342f] sm:text-2xl">Pembayaran pendaftaran belum berhasil.</h2>
                                <p class="mt-2 max-w-3xl text-sm leading-6 text-[#62756f]">
                                    Kirim ulang profil pasien untuk membuat transaksi pembayaran baru.
                                </p>
                            @else
                                <h2 class="mt-2 text-xl font-black leading-tight text-[#14342f] sm:text-2xl">Lengkapi profil pasien untuk mengaktifkan fitur klinik.</h2>
                                <p class="mt-2 max-w-3xl text-sm leading-6 text-[#62756f]">
                                    Akun Anda sudah dibuat, tetapi belum memiliki profil pasien. Kirim data profil dan selesaikan pembayaran pendaftaran.
                                </p>
                            @endif
                        </div>

                        @if($pengajuanPasien?->isMenungguPembayaran() && $pengajuanPasien?->transaksi)
                            <a href="{{ route('pasien.pembayaran.show', $pengajuanPasien->transaksi) }}" class="clinic-btn-primary w-full md:w-auto">
                                Bayar Rp1.000
                            </a>
                        @elseif(! $pengajuanPasien?->isMenungguPembayaran())
                            <a href="{{ route('pasien.pengajuan-pasien.create') }}" class="clinic-btn-primary w-full md:w-auto">
                                {{ $pengajuanPasien?->isPembayaranGagal() ? 'Ajukan Ulang' : 'Tambah Profil Pasien' }}
                            </a>
                        @else
                            <span class="rounded-lg bg-[#f3faf6] px-4 py-3 text-sm font-bold text-[#62756f]">
                                Dikirim {{ $pengajuanPasien->created_at->format('d M Y H:i') }}
                            </span>
                        @endif
                    </div>
                </section>
            @endif

            <section class="patient-dashboard-grid">
                <div class="patient-dashboard-main">
                <div class="clinic-surface overflow-hidden">
                    <div class="patient-dashboard-hero-grid p-5 sm:p-6">
                        <div class="flex min-w-0 flex-col justify-between gap-6">
                            <div>
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
                                    <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-md bg-[#14342f] text-2xl font-black text-white shadow-sm">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="clinic-kicker">Selamat datang</p>
                                        <h2 class="mt-2 break-words text-3xl font-black leading-tight text-[#14342f] sm:text-4xl">
                                            {{ $user->name }}
                                        </h2>
                                        <p class="mt-3 max-w-2xl text-sm leading-6 text-[#62756f] sm:text-base sm:leading-7">
                                            Pantau antrean aktif, pembayaran, profil keluarga, dan riwayat pemeriksaan dari satu halaman.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="grid gap-3 sm:grid-cols-3">
                                <a href="{{ $pasiens->isNotEmpty() ? route('pasien.antrean.create') : route('pasien.pengajuan-pasien.create') }}" id="btn-booking-antrean-card" class="clinic-btn-primary w-full px-4 text-center">
                                    <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V4m8 3V4M5 11h14M6 20h12a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2Z"/>
                                    </svg>
                                    <span>{{ $pasiens->isNotEmpty() ? 'Booking Antrean' : 'Ajukan Profil' }}</span>
                                </a>
                                <a href="{{ $pasiens->isNotEmpty() ? route('pasien.pembayaran.index') : route('pasien.pengajuan-pasien.create') }}" id="btn-pembayaran-qris" class="clinic-btn-secondary w-full px-4 text-center">
                                    <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M6 11h12M7 15h5m-6 4h12a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2Z"/>
                                    </svg>
                                    <span>{{ $pasiens->isNotEmpty() ? 'Pembayaran' : 'Status Verifikasi' }}</span>
                                </a>
                                <a href="{{ $pasiens->isNotEmpty() ? route('pasien.profil.index') : route('pasien.pengajuan-pasien.create') }}" class="clinic-btn-secondary w-full px-4 text-center">
                                    <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0ZM12 14a7 7 0 0 0-7 7h14a7 7 0 0 0-7-7Z"/>
                                    </svg>
                                    <span>{{ $pasiens->isNotEmpty() ? 'Profil Pasien' : 'Lengkapi Data' }}</span>
                                </a>
                            </div>
                        </div>

                        <div class="clinic-panel flex h-full flex-col justify-between">
                            @if($antreanAktif)
                                <div>
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="clinic-stat-label">Antrean aktif</p>
                                            <p class="mt-2 text-6xl font-black leading-none text-[#14342f]">
                                                {{ str_pad($antreanAktif->nomor_antrean, 3, '0', STR_PAD_LEFT) }}
                                            </p>
                                        </div>
                                        <x-status-badge type="antrean" :value="$antreanAktif->status" />
                                    </div>

                                    <div class="mt-5 grid gap-3 text-sm">
                                        <div class="rounded-lg bg-white p-4">
                                            <span class="text-xs font-bold text-[#62756f]">Pasien</span>
                                            <p class="mt-1 font-black text-[#14342f]">{{ $antreanAktif->pasien->nama_pasien }}</p>
                                        </div>
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
                                </div>

                                <a href="{{ route('pasien.antrean.tiket', $antreanAktif->kode_antrean) }}" class="mt-5 inline-flex w-full items-center justify-center rounded-md bg-[#14342f] px-4 py-3 text-sm font-bold text-white transition hover:bg-[#1f4b44]">
                                    Lihat Tiket QR
                                </a>
                            @else
                                <div class="flex h-full flex-col justify-between gap-5">
                                    <div class="flex items-start gap-4">
                                        <div class="clinic-icon-box h-12 w-12 shrink-0">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V4m8 3V4M5 11h14M6 20h12a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2Z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="clinic-kicker">Antrean</p>
                                            <h3 class="mt-2 text-xl font-black text-[#14342f]">Belum ada antrean aktif</h3>
                                            <p class="mt-2 text-sm leading-6 text-[#62756f]">Pilih jadwal kunjungan untuk mendapatkan nomor antrean dan tiket QR.</p>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-3 text-sm">
                                        <div class="flex items-center justify-between rounded-lg bg-white p-4">
                                            <span class="font-bold text-[#62756f]">Profil pasien</span>
                                            <span class="font-black text-[#14342f]">{{ $pasiens->count() }}</span>
                                        </div>
                                        <div class="flex items-center justify-between rounded-lg bg-white p-4">
                                            <span class="font-bold text-[#62756f]">Tagihan aktif</span>
                                            <span class="font-black text-[#14342f]">{{ $tagihanBelumLunas }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <section class="grid gap-4 md:grid-cols-3">
                    @foreach ($quickActions as $action)
                        <a href="{{ $action['route'] }}" id="{{ $action['id'] }}" class="clinic-action-card">
                            <span class="clinic-kicker">{{ $action['action_text'] }}</span>
                            <h3 class="mt-2 text-lg font-black text-[#14342f]">{{ $action['title'] }}</h3>
                            <p class="mt-2 flex-1 text-sm leading-6 text-[#62756f]">{{ $action['body'] }}</p>
                            <span class="mt-5 inline-flex items-center gap-2 text-sm font-black text-[#ef7b2d]">
                                Buka halaman
                                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7-7 7 7-7 7"/>
                                </svg>
                            </span>
                        </a>
                    @endforeach
                </section>

                <section class="clinic-surface p-5 sm:p-6">
                    <div class="grid gap-5 lg:grid-cols-[1fr_auto] lg:items-center">
                        <div>
                            <p class="clinic-kicker">Pemeriksaan terakhir</p>
                            @if($pemeriksaanTerakhir)
                                <div class="mt-3 grid gap-4 md:grid-cols-[minmax(0,1fr)_auto] md:items-start">
                                    <div>
                                        <h3 class="text-xl font-black text-[#14342f]">{{ $pemeriksaanTerakhir->dokter->nama_dokter }}</h3>
                                        <p class="mt-1 text-sm font-semibold text-[#62756f]">
                                            {{ $pemeriksaanTerakhir->pasien->nama_pasien }} - {{ $pemeriksaanTerakhir->tgl_pemeriksaan->format('d M Y') }}
                                        </p>
                                        <p class="mt-2 max-w-3xl text-sm leading-6 text-[#62756f]">{{ $pemeriksaanTerakhir->diagnosa }}</p>
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        <x-status-badge type="payment" :value="$pemeriksaanTerakhir->status_bayar" />
                                        @if($pemeriksaanTerakhir->resep)
                                            <x-status-badge type="pickup" :value="$pemeriksaanTerakhir->resep->status_ambil" />
                                        @endif
                                    </div>
                                </div>
                            @else
                                <h3 class="mt-2 text-xl font-black text-[#14342f]">Belum ada pemeriksaan tercatat</h3>
                                <p class="mt-1 text-sm leading-6 text-[#62756f]">Riwayat medis akan muncul setelah admin menyelesaikan pemeriksaan.</p>
                            @endif
                        </div>
                        <a href="{{ $pasiens->isNotEmpty() ? route('pasien.riwayat.index') : route('pasien.pengajuan-pasien.create') }}" class="clinic-btn-secondary w-full lg:w-auto">
                            {{ $pasiens->isNotEmpty() ? 'Lihat Riwayat' : 'Lengkapi Data' }}
                        </a>
                    </div>
                </section>
                </div>

                <aside class="patient-dashboard-sidebar">
                    <div class="clinic-surface p-5">
                        <div class="flex items-center justify-between gap-3">
                            <p class="clinic-kicker">Ringkasan</p>
                            <span class="rounded-md bg-[#e1f1e8] px-3 py-1 text-xs font-black text-[#14342f]">{{ $pasiens->count() }} profil</span>
                        </div>
                        <div class="patient-dashboard-summary-grid mt-4">
                            <div class="clinic-stat">
                                <span class="clinic-stat-label">Total antrean</span>
                                <p class="clinic-stat-value">{{ $jumlahAntrean }}</p>
                            </div>
                            <div class="clinic-stat">
                                <span class="clinic-stat-label">Riwayat medis</span>
                                <p class="clinic-stat-value">{{ $jumlahRiwayat }}</p>
                            </div>
                            <div class="clinic-stat">
                                <span class="clinic-stat-label">Tagihan aktif</span>
                                <p class="clinic-stat-value">{{ $tagihanBelumLunas }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="clinic-surface p-5">
                        <p class="clinic-kicker">Data akun</p>
                        <div class="patient-dashboard-account-grid mt-4 text-sm">
                            <div class="clinic-soft-row">
                                <span class="font-bold text-[#62756f]">Email</span>
                                <p class="mt-1 break-words font-semibold text-[#14342f]">{{ $user->email }}</p>
                            </div>
                            <div class="clinic-soft-row">
                                <span class="font-bold text-[#62756f]">Nomor HP</span>
                                <p class="mt-1 font-semibold text-[#14342f]">{{ $user->no_hp ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="patient-dashboard-account-actions mt-5">
                            <a href="{{ route('profile.edit') }}" class="clinic-btn-secondary w-full px-3 py-2 text-center" id="btn-edit-profile">
                                Edit Akun
                            </a>
                            <a href="{{ route('pasien.profil.index') }}" class="clinic-btn-secondary w-full px-3 py-2 text-center">
                                Profil Pasien
                            </a>
                        </div>
                    </div>
                </aside>
            </section>
        </div>
    </div>
</x-app-layout>
