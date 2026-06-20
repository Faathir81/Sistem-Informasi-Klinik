<x-app-layout>
    <style>
        .payment-page-content {
            max-width: 72rem;
        }

        .payment-intro-grid,
        .payment-card-head,
        .payment-meta-grid {
            display: grid;
            gap: 1rem;
        }

        @media (min-width: 640px) {
            .payment-meta-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (min-width: 768px) {
            .payment-intro-grid {
                grid-template-columns: minmax(0, 1fr) auto;
                align-items: center;
            }

            .payment-card-head {
                grid-template-columns: minmax(0, 1fr) 240px;
                align-items: start;
            }
        }
    </style>

    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="clinic-kicker">Keuangan</p>
                <h1 class="mt-1 text-2xl font-black text-[#14342f]">Pembayaran QRIS</h1>
            </div>
            <a href="{{ route('pasien.dashboard') }}" class="clinic-btn-secondary w-full sm:w-auto">
                Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="clinic-section payment-page-content space-y-5">
            <section class="clinic-card-solid p-6">
                <div class="payment-intro-grid">
                    <div>
                        <p class="clinic-kicker">Tagihan pemeriksaan</p>
                        <h2 class="mt-2 text-2xl font-black text-[#14342f]">Buat pembayaran QRIS.</h2>
                        <p class="mt-2 text-sm leading-6 text-[#62756f]">Tentukan biaya konsultasi, lalu sistem akan menjumlahkannya dengan total resep obat dan tindakan klinik.</p>
                    </div>
                    <div class="rounded-lg border border-orange-100 bg-[#fff7ed] px-4 py-3 text-sm">
                        <span class="font-bold text-[#a4531b]">Mode</span>
                        <p class="mt-1 font-black text-[#14342f]">Sandbox QRIS</p>
                    </div>
                </div>
            </section>

            @if (! $pasien)
                <div class="clinic-card-solid p-6 text-sm font-semibold leading-6 text-[#62756f]">
                    Akun Anda belum memiliki profil pasien aktif. Tambahkan profil pasien terlebih dahulu.
                </div>
            @elseif ($pemeriksaans->isEmpty())
                <div class="clinic-card-solid p-10 text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-lg bg-[#eef8f2] text-[#386258]">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M6 11h12M7 15h5m-6 4h12a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2Z"/>
                        </svg>
                    </div>
                    <h2 class="mt-5 text-xl font-black text-[#14342f]">Belum ada tagihan pemeriksaan</h2>
                    <p class="mt-2 text-sm leading-6 text-[#62756f]">Tagihan akan muncul setelah pemeriksaan dan resep dicatat oleh admin.</p>
                </div>
            @else
                <div class="grid gap-5">
                    @foreach ($pemeriksaans as $pemeriksaan)
                        @php
                            $totalObat = (float) ($pemeriksaan->resep?->total_harga_obat ?? 0);
                            $totalTindakan = $pemeriksaan->totalTindakan();
                            $biayaKonsultasi = old('biaya_konsultasi', (int) $pemeriksaan->biaya_konsultasi);
                            $tagihan = (float) $biayaKonsultasi + $totalObat + $totalTindakan;
                            $transaksi = $pemeriksaan->transaksi;
                        @endphp

                        <article class="clinic-card-solid overflow-hidden">
                            <div class="payment-card-head p-5 sm:p-6">
                                <div>
                                    <p class="clinic-kicker">Tagihan pemeriksaan</p>
                                    <h2 class="mt-2 text-2xl font-black text-[#14342f]">{{ $pemeriksaan->pasien->nama_pasien }}</h2>
                                    <p class="mt-2 text-sm leading-6 text-[#62756f]">Rincian biaya berdasarkan pemeriksaan, tindakan, dan resep yang tercatat.</p>
                                </div>
                                <div class="rounded-lg border border-[#d6e7dd] bg-[#f3faf6] p-4">
                                    <p class="text-sm font-bold text-[#62756f]">Total pembayaran</p>
                                    <p class="mt-1 text-2xl font-black text-[#14342f]" data-total-payment-summary>Rp {{ number_format($tagihan, 0, ',', '.') }}</p>
                                    <x-status-badge class="mt-3" type="payment" :value="$pemeriksaan->status_bayar" />
                                </div>
                            </div>

                            <div class="payment-meta-grid px-5 pb-5 sm:px-6 sm:pb-6">
                                <div class="clinic-soft-row">
                                    <span class="clinic-stat-label">Tanggal</span>
                                    <p class="mt-2 text-sm font-black text-[#14342f]">{{ $pemeriksaan->tgl_pemeriksaan->format('d M Y') }}</p>
                                </div>
                                <div class="clinic-soft-row">
                                    <span class="clinic-stat-label">Dokter</span>
                                    <p class="mt-2 text-sm font-black text-[#14342f]">{{ $pemeriksaan->dokter->nama_dokter }}</p>
                                </div>
                                <div class="clinic-soft-row">
                                    <span class="clinic-stat-label">Diagnosa</span>
                                    <p class="mt-2 text-sm font-semibold leading-6 text-[#14342f]">{{ $pemeriksaan->diagnosa }}</p>
                                </div>
                            </div>

                            @if ($transaksi)
                                <div class="border-t border-slate-100 bg-slate-50/80 px-5 py-4 sm:px-6">
                                    <div class="flex flex-col gap-3 text-sm sm:flex-row sm:items-center sm:justify-between">
                                        <div class="min-w-0">
                                            <span class="font-bold text-[#62756f]">Transaksi terakhir</span>
                                            <p class="mt-1 break-all font-mono font-black text-[#14342f]">{{ $transaksi->order_id }}</p>
                                        </div>
                                        <div class="flex shrink-0 items-center gap-3">
                                            <x-status-badge type="transaction" :value="$transaksi->status" :dot="false" />
                                            <a href="{{ route('pasien.pembayaran.show', $transaksi) }}" class="font-black text-[#ef7b2d] hover:text-[#c75f1d]">Lihat Detail</a>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($pemeriksaan->status_bayar !== \App\Enums\PaymentStatus::Lunas->value)
                                <form method="POST" action="{{ route('pasien.pembayaran.store', $pemeriksaan) }}" class="grid gap-5 border-t border-slate-100 p-5 sm:p-6 xl:grid-cols-[1fr_auto] xl:items-end" data-payment-form data-total-obat="{{ (int) round($totalObat) }}" data-total-tindakan="{{ (int) round($totalTindakan) }}">
                                    @csrf
                                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                                        <div>
                                            <label for="biaya-konsultasi-{{ $pemeriksaan->id }}" class="clinic-label block">Biaya konsultasi</label>
                                            <input id="biaya-konsultasi-{{ $pemeriksaan->id }}" name="biaya_konsultasi" type="number" min="0" step="1" value="{{ $biayaKonsultasi }}" class="clinic-field mt-2" data-consultation-input>
                                        </div>
                                        <div>
                                            <span class="clinic-label block">Total resep obat</span>
                                            <div class="mt-2 rounded-md border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-bold text-[#14342f]">
                                                Rp {{ number_format($totalObat, 0, ',', '.') }}
                                            </div>
                                        </div>
                                        <div>
                                            <span class="clinic-label block">Total tindakan</span>
                                            <div class="mt-2 rounded-md border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-bold text-[#14342f]">
                                                Rp {{ number_format($totalTindakan, 0, ',', '.') }}
                                            </div>
                                        </div>
                                        <div>
                                            <span class="clinic-label block">Total dibayarkan</span>
                                            <div class="mt-2 rounded-md border border-[#b8cec3] bg-[#f3faf6] px-3 py-2.5 text-sm font-black text-[#14342f]" data-total-payment>
                                                Rp {{ number_format($tagihan, 0, ',', '.') }}
                                            </div>
                                        </div>
                                        @error('biaya_konsultasi')
                                            <p class="sm:col-span-2 lg:col-span-4 text-sm font-semibold text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <button type="submit" class="clinic-btn-primary w-full xl:w-auto">
                                        Buat QRIS
                                    </button>
                                </form>
                            @endif
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <script>
        document.querySelectorAll('[data-payment-form]').forEach((form) => {
            const input = form.querySelector('[data-consultation-input]');
            const output = form.querySelector('[data-total-payment]');
            const summary = form.closest('article').querySelector('[data-total-payment-summary]');
            const totalObat = Number(form.dataset.totalObat || 0);
            const totalTindakan = Number(form.dataset.totalTindakan || 0);
            const formatter = new Intl.NumberFormat('id-ID');

            const updateTotal = () => {
                const biayaKonsultasi = Math.max(Number(input.value || 0), 0);
                const totalText = `Rp ${formatter.format(biayaKonsultasi + totalObat + totalTindakan)}`;
                output.textContent = totalText;
                summary.textContent = totalText;
            };

            input.addEventListener('input', updateTotal);
            updateTotal();
        });
    </script>
</x-app-layout>
