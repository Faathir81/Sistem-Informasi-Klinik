<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="clinic-kicker">Rekam medis</p>
                <h1 class="mt-1 text-2xl font-black text-[#14342f]">Riwayat Medis & Resep</h1>
            </div>
            <a href="{{ route('pasien.dashboard') }}" class="clinic-btn-secondary w-full sm:w-auto">
                Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="clinic-section space-y-5">
            <section class="clinic-card-solid p-6">
                <div class="grid gap-5 md:grid-cols-[1fr_auto] md:items-center">
                    <div>
                        <p class="clinic-kicker">Data pengobatan</p>
                        <h2 class="mt-2 text-2xl font-black text-[#14342f]">Pemeriksaan dan resep yang tercatat.</h2>
                        <p class="mt-2 text-sm leading-6 text-[#62756f]">Riwayat hanya menampilkan profil pasien yang terhubung dengan akun login saat ini.</p>
                    </div>
                    @if($pasien)
                        <div class="rounded-lg bg-[#f3faf6] p-4 text-sm">
                            <span class="font-bold text-[#62756f]">Profil Pasien</span>
                            <p class="mt-1 font-black text-[#14342f]">{{ $pasiens->count() }} profil terhubung</p>
                        </div>
                    @endif
                </div>
            </section>

            @if (! $pasien)
                <div class="clinic-card-solid p-6 text-sm font-semibold leading-6 text-[#62756f]">
                    Akun Anda belum memiliki profil pasien aktif. Silakan tambahkan profil pasien terlebih dahulu.
                </div>
            @elseif ($pemeriksaans->isEmpty())
                <div class="clinic-card-solid p-10 text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-lg bg-[#eef8f2] text-[#386258]">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l5 5v11a2 2 0 0 1-2 2Z"/>
                        </svg>
                    </div>
                    <h2 class="mt-5 text-xl font-black text-[#14342f]">Belum ada riwayat pemeriksaan</h2>
                    <p class="mt-2 text-sm leading-6 text-[#62756f]">Data akan muncul setelah admin menyelesaikan pemeriksaan medis.</p>
                </div>
            @else
                @foreach ($pemeriksaans as $pemeriksaan)
                    <article class="clinic-card-solid overflow-hidden">
                        <div class="border-b border-slate-100 p-6">
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                <div>
                                    <p class="text-sm font-bold text-[#62756f]">{{ $pemeriksaan->tgl_pemeriksaan->format('d M Y') }}</p>
                                    <h2 class="mt-1 text-2xl font-black text-[#14342f]">{{ $pemeriksaan->pasien->nama_pasien }}</h2>
                                    <p class="mt-1 text-sm font-semibold text-[#62756f]">{{ $pemeriksaan->dokter->nama_dokter }}</p>
                                    <p class="mt-1 text-sm font-semibold text-[#62756f]">{{ $pemeriksaan->dokter->spesialisasi }}</p>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <span class="clinic-badge-info">
                                        Konsultasi Rp {{ number_format($pemeriksaan->biaya_konsultasi, 0, ',', '.') }}
                                    </span>
                                    <x-status-badge type="payment" :value="$pemeriksaan->status_bayar" />
                                </div>
                            </div>
                        </div>

                        <div class="grid gap-4 p-6 md:grid-cols-3">
                            <div class="rounded-lg bg-[#f3faf6] p-4">
                                <span class="text-xs font-bold text-[#62756f]">Keluhan</span>
                                <p class="mt-2 text-sm font-semibold leading-6 text-[#14342f]">{{ $pemeriksaan->keluhan }}</p>
                            </div>
                            <div class="rounded-lg bg-[#f3faf6] p-4">
                                <span class="text-xs font-bold text-[#62756f]">Diagnosa</span>
                                <p class="mt-2 text-sm font-semibold leading-6 text-[#14342f]">{{ $pemeriksaan->diagnosa }}</p>
                            </div>
                            <div class="rounded-lg bg-[#f3faf6] p-4">
                                <span class="text-xs font-bold text-[#62756f]">Tindakan</span>
                                <p class="mt-2 text-sm font-semibold leading-6 text-[#14342f]">{{ $pemeriksaan->tindakan ?: '-' }}</p>
                            </div>
                        </div>

                        <div class="border-t border-slate-100 p-6">
                            <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                <h3 class="text-lg font-black text-[#14342f]">Tindakan Klinik</h3>
                                @if ($pemeriksaan->tindakanDetails->isNotEmpty())
                                    <span class="clinic-badge-info">
                                        Total Rp {{ number_format($pemeriksaan->totalTindakan(), 0, ',', '.') }}
                                    </span>
                                @endif
                            </div>

                            @if ($pemeriksaan->tindakanDetails->isEmpty())
                                <p class="rounded-lg bg-slate-50 p-4 text-sm font-semibold text-[#62756f]">Tidak ada tindakan klinik berbayar pada pemeriksaan ini.</p>
                            @else
                                <div class="mb-6 overflow-x-auto">
                                    <table class="clinic-table">
                                        <thead>
                                            <tr>
                                                <th>Tindakan</th>
                                                <th>Catatan</th>
                                                <th class="text-right">Tarif</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($pemeriksaan->tindakanDetails as $tindakan)
                                                <tr>
                                                    <td class="font-black text-[#14342f]">{{ $tindakan->nama_layanan }}</td>
                                                    <td class="font-semibold text-[#62756f]">{{ $tindakan->catatan ?: '-' }}</td>
                                                    <td class="text-right font-black text-[#14342f]">Rp {{ number_format($tindakan->tarif, 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif

                            <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                <h3 class="text-lg font-black text-[#14342f]">Resep Obat</h3>
                                @if ($pemeriksaan->resep)
                                    <x-status-badge type="pickup" :value="$pemeriksaan->resep->status_ambil" />
                                @endif
                            </div>

                            @if (! $pemeriksaan->resep || $pemeriksaan->resep->details->isEmpty())
                                <p class="rounded-lg bg-slate-50 p-4 text-sm font-semibold text-[#62756f]">Tidak ada resep obat pada pemeriksaan ini.</p>
                            @else
                                <div class="overflow-x-auto">
                                    <table class="clinic-table">
                                        <thead>
                                            <tr>
                                                <th>Obat</th>
                                                <th>Jumlah</th>
                                                <th>Aturan Pakai</th>
                                                <th class="text-right">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($pemeriksaan->resep->details as $detail)
                                                <tr>
                                                    <td class="font-black text-[#14342f]">{{ $detail->obat->nama_obat }}</td>
                                                    <td class="font-semibold text-[#62756f]">{{ $detail->jumlah }} {{ $detail->obat->satuan }}</td>
                                                    <td class="font-semibold text-[#62756f]">{{ $detail->aturan_pakai }}</td>
                                                    <td class="text-right font-black text-[#14342f]">Rp {{ number_format($detail->sub_total, 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3" class="pt-4 text-right font-black text-[#62756f]">Total Obat</td>
                                                <td class="pt-4 text-right text-lg font-black text-[#14342f]">
                                                    Rp {{ number_format($pemeriksaan->resep->total_harga_obat, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </article>
                @endforeach
            @endif
        </div>
    </div>
</x-app-layout>
