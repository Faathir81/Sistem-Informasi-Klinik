<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="clinic-kicker">Antrean</p>
                <h1 class="mt-1 text-2xl font-black text-[#14342f]">Riwayat Antrean Saya</h1>
            </div>
            <a href="{{ route('pasien.antrean.create') }}" id="btn-booking-baru" class="clinic-btn-primary">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"/>
                </svg>
                Booking Baru
            </a>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="clinic-section max-w-5xl space-y-4">
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

            @forelse($antreans as $antrean)
                <article class="clinic-card-solid clinic-hover-lift overflow-hidden">
                    <div class="grid gap-5 p-5 sm:grid-cols-[auto_1fr_auto] sm:items-center">
                        <div class="flex h-20 w-20 flex-col items-center justify-center rounded-lg bg-[#14342f] text-white shadow-sm">
                            <span class="text-xs font-bold uppercase tracking-[0.14em] text-white/60">No</span>
                            <span class="text-3xl font-black leading-none">{{ str_pad($antrean->nomor_antrean, 3, '0', STR_PAD_LEFT) }}</span>
                        </div>

                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="text-lg font-black text-[#14342f]">{{ $antrean->dokter->nama_dokter }}</h2>
                                <x-status-badge type="antrean" :value="$antrean->status" />
                            </div>
                            <p class="mt-1 text-sm font-semibold text-[#62756f]">{{ $antrean->dokter->spesialisasi }}</p>
                            <div class="mt-3 grid gap-2 text-sm text-[#46665f] md:grid-cols-3">
                                <span class="rounded-md bg-[#f3faf6] px-3 py-2 font-semibold">{{ $antrean->tanggal_kunjungan->isoFormat('dddd, D MMMM Y') }}</span>
                                <span class="rounded-md bg-[#f3faf6] px-3 py-2 font-semibold">{{ substr($antrean->jadwalDokter->jam_mulai, 0, 5) }} - {{ substr($antrean->jadwalDokter->jam_selesai, 0, 5) }} WIB</span>
                                <span class="truncate rounded-md bg-[#f3faf6] px-3 py-2 font-mono text-xs font-bold">{{ $antrean->kode_antrean }}</span>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2 sm:flex-col sm:items-stretch">
                            @if($antrean->status !== \App\Enums\AntreanStatus::Batal->value)
                                <a href="{{ route('pasien.antrean.tiket', $antrean->kode_antrean) }}" id="btn-lihat-tiket-{{ $antrean->id }}" class="clinic-btn-secondary min-h-10 px-4 py-2">
                                    Tiket QR
                                </a>
                            @endif
                            @if($antrean->status === \App\Enums\AntreanStatus::Menunggu->value)
                                <form action="{{ route('pasien.antrean.batal', $antrean->id) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan antrean ini?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" id="btn-batal-{{ $antrean->id }}" class="inline-flex min-h-10 w-full items-center justify-center rounded-md border border-red-200 px-4 py-2 text-sm font-bold text-red-600 transition hover:bg-red-50">
                                        Batalkan
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="clinic-card-solid p-10 text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-lg bg-[#eef8f2] text-[#386258]">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V4m8 3V4M5 11h14M6 20h12a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2Z"/>
                        </svg>
                    </div>
                    <h2 class="mt-5 text-xl font-black text-[#14342f]">Belum ada antrean</h2>
                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-[#62756f]">Booking antrean untuk mendapatkan nomor kunjungan dan tiket QR Code digital.</p>
                    <a href="{{ route('pasien.antrean.create') }}" class="clinic-btn-primary mt-6">
                        Booking Antrean
                    </a>
                </div>
            @endforelse

            <div class="pt-2">
                {{ $antreans->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
