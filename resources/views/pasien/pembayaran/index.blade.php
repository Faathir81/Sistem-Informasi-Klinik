<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="clinic-kicker">Keuangan</p>
                <h1 class="mt-1 text-2xl font-black text-[#14342f]">Pembayaran QRIS</h1>
            </div>
            <a href="{{ route('pasien.dashboard') }}" class="clinic-btn-secondary">
                Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="clinic-section space-y-5">
            <section class="clinic-card-solid p-6">
                <div class="grid gap-5 md:grid-cols-[1fr_auto] md:items-center">
                    <div>
                        <p class="clinic-kicker">Tagihan pemeriksaan</p>
                        <h2 class="mt-2 text-2xl font-black text-[#14342f]">Buat pembayaran QRIS Midtrans.</h2>
                        <p class="mt-2 text-sm leading-6 text-[#62756f]">Masukkan nominal manual sesuai tagihan klinik, lalu lanjutkan pembayaran lewat Snap Midtrans.</p>
                    </div>
                    <div class="rounded-lg bg-[#fff7ed] p-4 text-sm">
                        <span class="font-bold text-[#a4531b]">Mode</span>
                        <p class="mt-1 font-black text-[#14342f]">Sandbox QRIS</p>
                    </div>
                </div>
            </section>

            @if (! $pasien)
                <div class="clinic-card-solid p-6 text-sm font-semibold leading-6 text-[#62756f]">
                    Akun Anda belum terhubung dengan data pasien. Silakan hubungi admin klinik.
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
                            $tagihan = $pemeriksaan->totalTagihan();
                            $transaksi = $pemeriksaan->transaksi;
                        @endphp

                        <article class="clinic-card-solid overflow-hidden">
                            <div class="grid gap-5 p-6 lg:grid-cols-[1fr_auto] lg:items-start">
                                <div>
                                    <p class="text-sm font-bold text-[#62756f]">{{ $pemeriksaan->tgl_pemeriksaan->format('d M Y') }}</p>
                                    <h2 class="mt-1 text-xl font-black text-[#14342f]">{{ $pemeriksaan->dokter->nama_dokter }}</h2>
                                    <p class="mt-1 text-sm leading-6 text-[#62756f]">{{ $pemeriksaan->diagnosa }}</p>
                                </div>
                                <div class="rounded-lg bg-[#f3faf6] p-4 lg:min-w-64">
                                    <p class="text-sm font-bold text-[#62756f]">Estimasi tagihan</p>
                                    <p class="mt-1 text-2xl font-black text-[#14342f]">Rp {{ number_format($tagihan, 0, ',', '.') }}</p>
                                    <span class="mt-3 {{ $pemeriksaan->status_bayar === 'Lunas' ? 'clinic-badge-success' : 'clinic-badge-warning' }}">
                                        {{ str_replace('_', ' ', $pemeriksaan->status_bayar) }}
                                    </span>
                                </div>
                            </div>

                            @if ($transaksi)
                                <div class="mx-6 rounded-lg border border-slate-100 bg-slate-50 p-4 text-sm font-semibold text-[#62756f]">
                                    Transaksi terakhir:
                                    <span class="font-mono text-[#14342f]">{{ $transaksi->order_id }}</span>
                                    <span class="font-black text-[#14342f]">({{ $transaksi->status }})</span>
                                    <a href="{{ route('pasien.pembayaran.show', $transaksi) }}" class="ml-2 font-black text-[#ef7b2d] hover:text-[#c75f1d]">Detail</a>
                                </div>
                            @endif

                            @if ($pemeriksaan->status_bayar !== 'Lunas')
                                <form method="POST" action="{{ route('pasien.pembayaran.store', $pemeriksaan) }}" class="grid gap-4 p-6 md:grid-cols-[1fr_auto] md:items-end">
                                    @csrf
                                    <div>
                                        <label for="amount-{{ $pemeriksaan->id }}" class="clinic-label block">Nominal yang dibayarkan</label>
                                        <input id="amount-{{ $pemeriksaan->id }}" name="amount" type="number" min="1000" value="{{ old('amount', (int) $tagihan) }}" class="clinic-field mt-2">
                                        @error('amount')
                                            <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <button type="submit" class="clinic-btn-primary">
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
</x-app-layout>
