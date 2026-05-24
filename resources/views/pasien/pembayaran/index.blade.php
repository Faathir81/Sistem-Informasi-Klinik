<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pembayaran QRIS') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800">Tagihan Pemeriksaan</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Masukkan nominal manual sesuai tagihan klinik, lalu sistem akan membuat pembayaran QRIS Midtrans.
                    </p>
                </div>
            </div>

            @if (! $pasien)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-sm text-gray-600">
                        Akun Anda belum terhubung dengan data pasien. Silakan hubungi admin klinik.
                    </div>
                </div>
            @elseif ($pemeriksaans->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-sm text-gray-600">
                        Belum ada tagihan pemeriksaan.
                    </div>
                </div>
            @else
                @foreach ($pemeriksaans as $pemeriksaan)
                    @php
                        $tagihan = $pemeriksaan->totalTagihan();
                        $transaksi = $pemeriksaan->transaksi;
                    @endphp

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 space-y-5">
                            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                                <div>
                                    <p class="text-sm text-gray-400">{{ $pemeriksaan->tgl_pemeriksaan->format('d M Y') }}</p>
                                    <h3 class="text-lg font-bold text-gray-800 mt-1">{{ $pemeriksaan->dokter->nama_dokter }}</h3>
                                    <p class="text-sm text-gray-500">{{ $pemeriksaan->diagnosa }}</p>
                                </div>
                                <div class="text-left md:text-right">
                                    <p class="text-sm text-gray-500">Estimasi tagihan</p>
                                    <p class="text-xl font-bold text-gray-900">Rp {{ number_format($tagihan, 0, ',', '.') }}</p>
                                    <span class="inline-flex mt-2 text-xs font-semibold {{ $pemeriksaan->status_bayar === 'Lunas' ? 'text-green-700 bg-green-50' : 'text-amber-700 bg-amber-50' }} px-3 py-1 rounded-full">
                                        {{ str_replace('_', ' ', $pemeriksaan->status_bayar) }}
                                    </span>
                                </div>
                            </div>

                            @if ($transaksi)
                                <div class="rounded-lg bg-gray-50 p-4 text-sm text-gray-700">
                                    Transaksi terakhir:
                                    <span class="font-mono">{{ $transaksi->order_id }}</span>
                                    <span class="font-semibold">({{ $transaksi->status }})</span>
                                </div>
                            @endif

                            @if ($pemeriksaan->status_bayar !== 'Lunas')
                                <form method="POST" action="{{ route('pasien.pembayaran.store', $pemeriksaan) }}" class="grid grid-cols-1 md:grid-cols-[1fr_auto] gap-3">
                                    @csrf
                                    <div>
                                        <label for="amount-{{ $pemeriksaan->id }}" class="block text-sm font-medium text-gray-700">Nominal yang dibayarkan</label>
                                        <input id="amount-{{ $pemeriksaan->id }}" name="amount" type="number" min="1000" value="{{ old('amount', (int) $tagihan) }}"
                                               class="mt-1 block w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500">
                                        @error('amount')
                                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <button type="submit" class="self-end inline-flex items-center justify-center rounded-lg bg-amber-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-amber-600 transition">
                                        Buat QRIS
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</x-app-layout>
