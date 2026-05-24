<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                📋 Riwayat Antrean Saya
            </h2>
            <a href="{{ route('pasien.antrean.create') }}"
               id="btn-booking-baru"
               class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Booking Antrean Baru
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg text-sm">
                    ✅ {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
                    ❌ {{ session('error') }}
                </div>
            @endif

            @forelse($antreans as $antrean)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex items-center gap-4">
                            {{-- Nomor Antrean Badge --}}
                            <div class="w-16 h-16 rounded-xl bg-emerald-600 text-white flex flex-col items-center justify-center flex-shrink-0 shadow">
                                <span class="text-xs">No.</span>
                                <span class="text-2xl font-bold leading-none">{{ $antrean->nomor_antrean }}</span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">{{ $antrean->dokter->nama_dokter }}</p>
                                <p class="text-sm text-gray-500">{{ $antrean->dokter->spesialisasi }}</p>
                                <p class="text-xs text-gray-400 mt-1">
                                    📅 {{ $antrean->tanggal_kunjungan->isoFormat('dddd, D MMMM Y') }} &bull;
                                    🕐 {{ substr($antrean->jadwalDokter->jam_mulai, 0, 5) }} - {{ substr($antrean->jadwalDokter->jam_selesai, 0, 5) }}
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5 font-mono">Kode: {{ $antrean->kode_antrean }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 sm:flex-col sm:items-end">
                            {{-- Status Badge --}}
                            @php
                                $statusColor = match($antrean->status) {
                                    'Menunggu'  => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                                    'Dipanggil' => 'bg-blue-100 text-blue-800 border-blue-300',
                                    'Selesai'   => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                    'Batal'     => 'bg-gray-100 text-gray-500 border-gray-300',
                                    default     => 'bg-gray-100 text-gray-500',
                                };
                                $statusIcon = match($antrean->status) {
                                    'Menunggu'  => '⏳',
                                    'Dipanggil' => '📢',
                                    'Selesai'   => '✅',
                                    'Batal'     => '❌',
                                    default     => '❓',
                                };
                            @endphp
                            <span class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1 rounded-full border {{ $statusColor }}">
                                {{ $statusIcon }} {{ $antrean->status }}
                            </span>

                            <div class="flex gap-2">
                                @if($antrean->status !== 'Batal')
                                    <a href="{{ route('pasien.antrean.tiket', $antrean->kode_antrean) }}"
                                       id="btn-lihat-tiket-{{ $antrean->id }}"
                                       class="text-xs text-emerald-600 hover:text-emerald-800 font-medium border border-emerald-300 px-3 py-1 rounded-lg hover:bg-emerald-50 transition">
                                        🎫 Lihat Tiket
                                    </a>
                                @endif
                                @if($antrean->status === 'Menunggu')
                                    <form action="{{ route('pasien.antrean.batal', $antrean->id) }}" method="POST"
                                          onsubmit="return confirm('Yakin ingin membatalkan antrean ini?')">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                                id="btn-batal-{{ $antrean->id }}"
                                                class="text-xs text-red-500 hover:text-red-700 font-medium border border-red-200 px-3 py-1 rounded-lg hover:bg-red-50 transition">
                                            Batalkan
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-12 text-center">
                        <div class="text-5xl mb-3">🗓️</div>
                        <h3 class="text-gray-700 font-semibold text-lg">Belum ada riwayat antrean</h3>
                        <p class="text-gray-400 text-sm mt-1 mb-5">Booking antrean sekarang untuk mendapatkan QR Code digital Anda.</p>
                        <a href="{{ route('pasien.antrean.create') }}"
                           class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition">
                            + Booking Antrean Sekarang
                        </a>
                    </div>
                </div>
            @endforelse

            {{ $antreans->links() }}
        </div>
    </div>
</x-app-layout>
