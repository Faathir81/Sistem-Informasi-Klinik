<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('pasien.antrean.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                🎫 Tiket Antrean
            </h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg text-sm mb-5 text-center">
                    ✅ {{ session('success') }}
                </div>
            @endif

            {{-- Kartu Tiket --}}
            <div class="bg-white shadow-lg rounded-2xl overflow-hidden" id="tiket-card">

                {{-- Header Tiket --}}
                <div class="bg-emerald-600 text-white px-6 py-5 text-center">
                    <p class="text-emerald-200 text-xs uppercase tracking-widest font-medium mb-1">Klinik Ar-Ridlo</p>
                    <h1 class="text-2xl font-bold">Tiket Antrean</h1>
                    <p class="text-emerald-100 text-sm mt-1">
                        {{ $antrean->tanggal_kunjungan->isoFormat('dddd, D MMMM Y') }}
                    </p>
                </div>

                {{-- Nomor Antrean Besar --}}
                <div class="text-center py-6 border-b border-dashed border-gray-200">
                    <p class="text-xs text-gray-400 uppercase tracking-widest mb-1">Nomor Antrean</p>
                    <div class="text-8xl font-extrabold text-emerald-600 leading-none">
                        {{ str_pad($antrean->nomor_antrean, 3, '0', STR_PAD_LEFT) }}
                    </div>

                    {{-- Status Badge --}}
                    @php
                        $statusColor = match($antrean->status) {
                            'Menunggu'  => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                            'Dipanggil' => 'bg-blue-100 text-blue-800 border-blue-300',
                            'Selesai'   => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                            'Batal'     => 'bg-gray-100 text-gray-500 border-gray-300',
                        };
                        $statusIcon = match($antrean->status) {
                            'Menunggu'  => '⏳',
                            'Dipanggil' => '📢',
                            'Selesai'   => '✅',
                            'Batal'     => '❌',
                        };
                    @endphp
                    <span class="inline-flex items-center gap-1 text-xs font-semibold px-4 py-1.5 rounded-full border mt-3 {{ $statusColor }}">
                        {{ $statusIcon }} {{ $antrean->status }}
                    </span>
                </div>

                {{-- Detail Dokter & Jadwal --}}
                <div class="px-6 py-4 space-y-2 text-sm border-b border-dashed border-gray-200">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Dokter</span>
                        <span class="font-semibold text-gray-800 text-right">{{ $antrean->dokter->nama_dokter }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Spesialisasi</span>
                        <span class="text-gray-700">{{ $antrean->dokter->spesialisasi }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Jam Praktek</span>
                        <span class="text-gray-700">
                            {{ substr($antrean->jadwalDokter->jam_mulai, 0, 5) }} –
                            {{ substr($antrean->jadwalDokter->jam_selesai, 0, 5) }} WIB
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Pasien</span>
                        <span class="font-semibold text-gray-800">{{ $antrean->pasien->nama_pasien }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">No. Rekam Medis</span>
                        <span class="font-mono text-gray-700 text-xs">{{ $antrean->pasien->no_rekam_medis }}</span>
                    </div>
                </div>

                {{-- QR Code --}}
                <div class="px-6 py-6 text-center" id="qrcode-section">
                    <p class="text-xs text-gray-400 mb-3">Tunjukkan QR Code ini kepada petugas klinik</p>
                    <div class="inline-block p-3 bg-white border-2 border-gray-200 rounded-xl shadow-sm">
                        {!! QrCode::size(180)->format('svg')->generate($antrean->kode_antrean) !!}
                    </div>
                    <p class="font-mono text-xs text-gray-400 mt-3 tracking-widest">{{ $antrean->kode_antrean }}</p>
                </div>

                {{-- Tombol Aksi --}}
                <div class="px-6 pb-6 space-y-2">
                    <button onclick="window.print()"
                            id="btn-print-tiket"
                            class="w-full flex items-center justify-center gap-2 border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium text-sm py-2.5 rounded-lg transition">
                        🖨️ Cetak / Simpan PDF
                    </button>

                    @if($antrean->status === 'Menunggu')
                        <form action="{{ route('pasien.antrean.batal', $antrean->id) }}" method="POST"
                              onsubmit="return confirm('Yakin ingin membatalkan antrean ini?')">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    id="btn-batal-dari-tiket"
                                    class="w-full text-red-500 hover:text-red-700 text-sm py-2 rounded-lg hover:bg-red-50 transition">
                                Batalkan Antrean Ini
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="mt-4 text-center">
                <a href="{{ route('pasien.antrean.index') }}"
                   class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                    ← Kembali ke Riwayat Antrean
                </a>
            </div>
        </div>
    </div>

    <style>
        @media print {
            header, nav, .no-print, button { display: none !important; }
            body { background: white; }
            #tiket-card { box-shadow: none; border: 1px solid #e5e7eb; }
        }
    </style>
</x-app-layout>
