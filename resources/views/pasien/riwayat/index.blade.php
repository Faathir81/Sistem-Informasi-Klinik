<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Medis & Resep') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">Riwayat Pengobatan</h3>
                            <p class="text-sm text-gray-500 mt-1">
                                Data pemeriksaan dan resep yang tercatat atas nama pasien login.
                            </p>
                        </div>
                        <a href="{{ route('pasien.dashboard') }}"
                           class="inline-flex items-center justify-center text-sm font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 px-4 py-2 rounded-lg transition">
                            Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </div>

            @if (! $pasien)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-sm text-gray-600">
                        Akun Anda belum terhubung dengan data pasien. Silakan hubungi admin klinik untuk menghubungkan akun dengan nomor rekam medis.
                    </div>
                </div>
            @elseif ($pemeriksaans->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-sm text-gray-600">
                        Belum ada riwayat pemeriksaan dan resep yang tercatat.
                    </div>
                </div>
            @else
                @foreach ($pemeriksaans as $pemeriksaan)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 space-y-5">
                            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3">
                                <div>
                                    <div class="text-sm text-gray-400">
                                        {{ $pemeriksaan->tgl_pemeriksaan->format('d M Y') }}
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-800 mt-1">
                                        {{ $pemeriksaan->dokter->nama_dokter }}
                                    </h3>
                                    <p class="text-sm text-gray-500">{{ $pemeriksaan->dokter->spesialisasi }}</p>
                                </div>
                                <div class="flex flex-wrap gap-2 text-xs">
                                    <span class="font-semibold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full">
                                        Konsultasi Rp {{ number_format($pemeriksaan->biaya_konsultasi, 0, ',', '.') }}
                                    </span>
                                    <span class="font-semibold {{ $pemeriksaan->status_bayar === 'Lunas' ? 'text-green-700 bg-green-50' : 'text-amber-700 bg-amber-50' }} px-3 py-1 rounded-full">
                                        {{ str_replace('_', ' ', $pemeriksaan->status_bayar) }}
                                    </span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-400">Keluhan</span>
                                    <p class="font-medium text-gray-800 mt-1">{{ $pemeriksaan->keluhan }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-400">Diagnosa</span>
                                    <p class="font-medium text-gray-800 mt-1">{{ $pemeriksaan->diagnosa }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-400">Tindakan</span>
                                    <p class="font-medium text-gray-800 mt-1">{{ $pemeriksaan->tindakan ?: '-' }}</p>
                                </div>
                            </div>

                            <div class="border-t border-gray-100 pt-5">
                                <div class="flex items-center justify-between gap-4 mb-3">
                                    <h4 class="font-semibold text-gray-800">Resep Obat</h4>
                                    @if ($pemeriksaan->resep)
                                        <span class="text-xs font-semibold text-orange-700 bg-orange-50 px-3 py-1 rounded-full">
                                            {{ str_replace('_', ' ', $pemeriksaan->resep->status_ambil) }}
                                        </span>
                                    @endif
                                </div>

                                @if (! $pemeriksaan->resep || $pemeriksaan->resep->details->isEmpty())
                                    <p class="text-sm text-gray-500">Tidak ada resep obat pada pemeriksaan ini.</p>
                                @else
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full text-sm">
                                            <thead>
                                                <tr class="text-left text-gray-400 border-b border-gray-100">
                                                    <th class="py-2 pr-4 font-medium">Obat</th>
                                                    <th class="py-2 pr-4 font-medium">Jumlah</th>
                                                    <th class="py-2 pr-4 font-medium">Aturan Pakai</th>
                                                    <th class="py-2 pr-4 font-medium text-right">Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100">
                                                @foreach ($pemeriksaan->resep->details as $detail)
                                                    <tr>
                                                        <td class="py-3 pr-4 font-medium text-gray-800">{{ $detail->obat->nama_obat }}</td>
                                                        <td class="py-3 pr-4 text-gray-600">{{ $detail->jumlah }} {{ $detail->obat->satuan }}</td>
                                                        <td class="py-3 pr-4 text-gray-600">{{ $detail->aturan_pakai }}</td>
                                                        <td class="py-3 pr-4 text-gray-800 text-right">Rp {{ number_format($detail->sub_total, 0, ',', '.') }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="3" class="pt-4 pr-4 text-right font-semibold text-gray-700">Total Obat</td>
                                                    <td class="pt-4 pr-4 text-right font-bold text-gray-900">
                                                        Rp {{ number_format($pemeriksaan->resep->total_harga_obat, 0, ',', '.') }}
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</x-app-layout>
