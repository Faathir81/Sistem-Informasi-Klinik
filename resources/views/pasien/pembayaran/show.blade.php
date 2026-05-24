<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Pembayaran') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 space-y-6">
                    <div>
                        <p class="text-sm text-gray-400">Order ID</p>
                        <h3 class="font-mono text-lg font-bold text-gray-900">{{ $transaksi->order_id }}</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-400">Pasien</span>
                            <p class="font-medium text-gray-800 mt-1">{{ $transaksi->pemeriksaan->pasien->nama_pasien }}</p>
                        </div>
                        <div>
                            <span class="text-gray-400">Dokter</span>
                            <p class="font-medium text-gray-800 mt-1">{{ $transaksi->pemeriksaan->dokter->nama_dokter }}</p>
                        </div>
                        <div>
                            <span class="text-gray-400">Nominal</span>
                            <p class="font-bold text-gray-900 mt-1">Rp {{ number_format($transaksi->amount, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <span class="text-gray-400">Status</span>
                            <p class="font-bold text-gray-900 mt-1">{{ $transaksi->status }}</p>
                        </div>
                    </div>

                    @if ($transaksi->status === 'SETTLEMENT')
                        <div class="rounded-lg bg-green-50 p-4 text-sm font-medium text-green-700">
                            Pembayaran sudah lunas.
                        </div>
                    @elseif ($transaksi->snap_token && $clientKey)
                        <button id="pay-button" type="button" class="w-full rounded-lg bg-amber-500 px-5 py-3 text-sm font-semibold text-white hover:bg-amber-600 transition">
                            Bayar dengan QRIS Midtrans
                        </button>
                        <a href="{{ $transaksi->snap_url }}" target="_blank" class="block text-center text-sm font-medium text-amber-700 hover:text-amber-800">
                            Buka halaman pembayaran Midtrans
                        </a>
                    @else
                        <div class="rounded-lg bg-red-50 p-4 text-sm font-medium text-red-700">
                            Token pembayaran belum tersedia. Silakan buat ulang pembayaran dari halaman pembayaran.
                        </div>
                    @endif

                    <a href="{{ route('pasien.pembayaran.index') }}" class="block text-center text-sm font-medium text-gray-600 hover:text-gray-800">
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
