<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('pasien.antrean.index') }}" class="clinic-btn-quiet px-2" aria-label="Kembali">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <p class="clinic-kicker">Tiket digital</p>
                <h1 class="mt-1 text-2xl font-black text-[#14342f]">Tiket Antrean</h1>
            </div>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="clinic-section max-w-5xl">
            @if(session('success'))
                <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-center text-sm font-semibold text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid gap-6 lg:grid-cols-[1fr_0.8fr] lg:items-start">
                <section class="clinic-card-solid overflow-hidden" id="tiket-card">
                    <div class="bg-[#14342f] p-6 text-white sm:p-8">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#f8b37d]">Klinik Ar-Ridlo</p>
                                <h2 class="mt-2 text-3xl font-black">Tiket Antrean</h2>
                                <p class="mt-2 text-sm font-semibold text-white/70">{{ $antrean->tanggal_kunjungan->isoFormat('dddd, D MMMM Y') }}</p>
                            </div>
                            <x-status-badge type="antrean" :value="$antrean->status" contrast />
                        </div>
                    </div>

                    <div class="grid gap-6 p-6 sm:p-8 md:grid-cols-[0.78fr_1.22fr]">
                        <div class="rounded-lg border border-[#d6e7dd] bg-[#f3faf6] p-6 text-center">
                            <p class="text-xs font-bold uppercase tracking-[0.16em] text-[#62756f]">Nomor Antrean</p>
                            <p class="mt-4 text-8xl font-black leading-none text-[#14342f]">
                                {{ str_pad($antrean->nomor_antrean, 3, '0', STR_PAD_LEFT) }}
                            </p>
                            <p class="mt-4 font-mono text-xs font-bold tracking-[0.18em] text-[#62756f]">{{ $antrean->kode_antrean }}</p>
                        </div>

                        <div class="space-y-4">
                            <div class="grid gap-3 sm:grid-cols-2">
                                <div class="rounded-lg bg-slate-50 p-4">
                                    <span class="text-xs font-bold text-[#62756f]">Dokter</span>
                                    <p class="mt-1 font-black text-[#14342f]">{{ $antrean->dokter->nama_dokter }}</p>
                                </div>
                                <div class="rounded-lg bg-slate-50 p-4">
                                    <span class="text-xs font-bold text-[#62756f]">Spesialisasi</span>
                                    <p class="mt-1 font-black text-[#14342f]">{{ $antrean->dokter->spesialisasi }}</p>
                                </div>
                                <div class="rounded-lg bg-slate-50 p-4">
                                    <span class="text-xs font-bold text-[#62756f]">Jam Praktek</span>
                                    <p class="mt-1 font-black text-[#14342f]">{{ substr($antrean->jadwalDokter->jam_mulai, 0, 5) }} - {{ substr($antrean->jadwalDokter->jam_selesai, 0, 5) }} WIB</p>
                                </div>
                            </div>

                            <div class="rounded-lg border border-[#d6e7dd] bg-white p-4">
                                <span class="text-xs font-bold text-[#62756f]">Pasien</span>
                                <p class="mt-1 text-lg font-black text-[#14342f]">{{ $antrean->pasien->nama_pasien }}</p>
                            </div>
                        </div>
                    </div>
                </section>

                <aside class="clinic-card-solid p-6" id="qrcode-section">
                    <div class="text-center">
                        <p class="clinic-kicker">QR Code</p>
                        <h2 class="mt-2 text-2xl font-black text-[#14342f]">Tunjukkan kepada petugas</h2>
                        <p class="mt-2 text-sm leading-6 text-[#62756f]">QR ini memuat kode antrean unik untuk verifikasi kunjungan.</p>
                    </div>

                    <div class="mx-auto mt-6 flex max-w-[260px] justify-center rounded-lg border border-[#d6e7dd] bg-white p-4 shadow-[0_18px_40px_rgba(20,52,47,0.08)]">
                        {!! QrCode::size(210)->format('svg')->generate($antrean->kode_antrean) !!}
                    </div>

                    <div class="mt-6 grid gap-3">
                        <a href="{{ route('pasien.antrean.tiket.pdf', $antrean->kode_antrean) }}" id="btn-print-tiket" class="clinic-btn-primary w-full">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8V4h10v4m-9 9H6a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-2m-8 0h8v3H8v-3Z"/>
                            </svg>
                            Cetak / Simpan PDF
                        </a>

                        @if($antrean->status === \App\Enums\AntreanStatus::Menunggu->value)
                            <form action="{{ route('pasien.antrean.batal', $antrean->id) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan antrean ini?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit" id="btn-batal-dari-tiket" class="inline-flex min-h-11 w-full items-center justify-center rounded-md border border-red-200 px-5 py-2.5 text-sm font-bold text-red-600 transition hover:bg-red-50">
                                    Batalkan Antrean
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('pasien.antrean.index') }}" class="clinic-btn-secondary w-full">
                            Kembali
                        </a>
                    </div>
                </aside>
            </div>
        </div>
    </div>

    <style>
        @media print {
            nav,
            header,
            button,
            a {
                display: none !important;
            }

            body,
            .clinic-page {
                background: #ffffff !important;
            }

            #tiket-card {
                display: block !important;
                box-shadow: none !important;
                border: 1px solid #d9e5df !important;
            }
        }
    </style>
</x-app-layout>
