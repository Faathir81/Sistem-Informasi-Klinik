<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="clinic-kicker">Detail transaksi</p>
            <h1 class="mt-1 text-2xl font-black text-[#14342f]">Pembayaran QRIS</h1>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="clinic-section max-w-4xl">
            @if(session('status'))
                <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="clinic-card-solid overflow-hidden">
                <div class="bg-[#14342f] p-6 text-white sm:p-8">
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#f8b37d]">Order ID</p>
                    <h2 class="mt-2 break-words font-mono text-2xl font-black">{{ $transaksi->order_id }}</h2>
                </div>

                <div class="grid gap-4 p-6 sm:p-8 md:grid-cols-2">
                    <div class="rounded-lg bg-[#f3faf6] p-4">
                        <span class="text-xs font-bold text-[#62756f]">Pasien</span>
                        <p class="mt-1 font-black text-[#14342f]">{{ $transaksi->pemeriksaan->pasien->nama_pasien }}</p>
                    </div>
                    <div class="rounded-lg bg-[#f3faf6] p-4">
                        <span class="text-xs font-bold text-[#62756f]">Dokter</span>
                        <p class="mt-1 font-black text-[#14342f]">{{ $transaksi->pemeriksaan->dokter->nama_dokter }}</p>
                    </div>
                    <div class="rounded-lg bg-[#fff7ed] p-4">
                        <span class="text-xs font-bold text-[#a4531b]">Nominal</span>
                        <p class="mt-1 text-2xl font-black text-[#14342f]">Rp {{ number_format($transaksi->amount, 0, ',', '.') }}</p>
                    </div>
                    <div class="rounded-lg bg-sky-50 p-4">
                        <span class="text-xs font-bold text-sky-700">Status</span>
                        <p class="mt-1 text-2xl font-black text-[#14342f]">{{ $transaksi->status }}</p>
                    </div>
                </div>

                <div class="border-t border-slate-100 p-6 sm:p-8">
                    @if ($transaksi->status === 'SETTLEMENT')
                        <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-700">
                            Pembayaran sudah lunas.
                        </div>
                    @elseif ($transaksi->snap_token && $clientKey)
                        <div class="grid gap-3">
                            <button id="pay-button" type="button" class="clinic-btn-primary w-full">
                                Bayar dengan QRIS Midtrans
                            </button>
                            <a href="{{ $transaksi->snap_url }}" target="_blank" class="clinic-btn-secondary w-full">
                                Buka Halaman Midtrans
                            </a>
                        </div>
                    @else
                        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm font-bold text-red-700">
                            Token pembayaran belum tersedia. Silakan buat ulang pembayaran dari halaman pembayaran.
                        </div>
                    @endif

                    <a href="{{ route('pasien.pembayaran.index') }}" class="mt-6 inline-flex text-sm font-black text-[#ef7b2d] hover:text-[#c75f1d]">
                        Kembali ke daftar pembayaran
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if ($transaksi->snap_token && $clientKey && $transaksi->status !== 'SETTLEMENT')
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ $clientKey }}"></script>
        <script>
            document.getElementById('pay-button').addEventListener('click', function () {
                window.snap.pay('{{ $transaksi->snap_token }}', {
                    onSuccess: function () { window.location.reload(); },
                    onPending: function () { window.location.reload(); },
                    onError: function () { window.location.reload(); },
                    onClose: function () {}
                });
            });
        </script>
    @endif
</x-app-layout>
