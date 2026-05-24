<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('pasien.antrean.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                📅 Booking Antrean
            </h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            {{-- Jika sudah ada antrean aktif hari ini --}}
            @if($antreanHariIni)
                <div class="bg-yellow-50 border border-yellow-300 rounded-xl p-5 mb-6">
                    <p class="text-yellow-800 font-semibold text-sm">⚠️ Anda sudah memiliki antrean aktif hari ini</p>
                    <p class="text-yellow-700 text-xs mt-1">
                        Nomor antrean #{{ $antreanHariIni->nomor_antrean }} - {{ $antreanHariIni->dokter->nama_dokter }}
                        &bull; Status: {{ $antreanHariIni->status }}
                    </p>
                    <a href="{{ route('pasien.antrean.tiket', $antreanHariIni->kode_antrean) }}"
                       class="inline-block mt-3 text-xs font-medium text-yellow-800 border border-yellow-400 px-3 py-1.5 rounded-lg hover:bg-yellow-100 transition">
                        🎫 Lihat Tiket Saya
                    </a>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm mb-5">
                    ❌ {{ session('error') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-xl overflow-hidden">
                <div class="px-6 pt-6 pb-2 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">Isi Form Booking</h3>
                    <p class="text-sm text-gray-400 mt-0.5">Pilih dokter, tanggal, dan jadwal yang tersedia.</p>
                </div>

                <form action="{{ route('pasien.antrean.store') }}" method="POST" class="p-6 space-y-5" id="form-booking">
                    @csrf

                    {{-- Pilih Tanggal --}}
                    <div>
                        <label for="tanggal_kunjungan" class="block text-sm font-medium text-gray-700 mb-1">
                            Tanggal Kunjungan <span class="text-red-500">*</span>
                        </label>
                        <input type="date"
                               id="tanggal_kunjungan"
                               name="tanggal_kunjungan"
                               min="{{ today()->toDateString() }}"
                               value="{{ old('tanggal_kunjungan', today()->toDateString()) }}"
                               class="w-full border-gray-300 rounded-lg text-sm shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                               required>
                    </div>

                    {{-- Pilih Dokter --}}
                    <div>
                        <label for="dokter_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Dokter <span class="text-red-500">*</span>
                        </label>
                        <select id="dokter_id" name="dokter_id"
                                class="w-full border-gray-300 rounded-lg text-sm shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                                required>
                            <option value="">-- Pilih Dokter --</option>
                            @foreach($dokters as $dokter)
                                <option value="{{ $dokter->id }}" {{ old('dokter_id') == $dokter->id ? 'selected' : '' }}>
                                    {{ $dokter->nama_dokter }} ({{ $dokter->spesialisasi }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Pilih Jadwal (dinamis via JS) --}}
                    <div id="jadwal-wrapper" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Jadwal Tersedia <span class="text-red-500">*</span>
                        </label>
                        <div id="jadwal-list" class="space-y-2">
                            {{-- Diisi oleh JavaScript --}}
                        </div>
                        <input type="hidden" id="jadwal_dokter_id" name="jadwal_dokter_id">
                    </div>

                    <div id="jadwal-kosong" class="hidden text-sm text-orange-600 bg-orange-50 border border-orange-200 rounded-lg px-4 py-3">
                        ℹ️ Tidak ada jadwal praktek untuk dokter ini pada hari yang dipilih.
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                                id="btn-submit-booking"
                                class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 rounded-lg transition text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                            🎫 Ambil Nomor Antrean
                        </button>
                    </div>
                </form>
            </div>

            {{-- Info pasien --}}
            <div class="mt-4 bg-gray-50 rounded-xl px-5 py-4 text-sm text-gray-500">
                <span class="font-medium text-gray-700">Pasien:</span>
                {{ $pasien->nama_pasien }} &bull; No. RM: <span class="font-mono">{{ $pasien->no_rekam_medis }}</span>
            </div>
        </div>
    </div>

    {{-- Script untuk load jadwal secara dinamis --}}
    <script>
        const dokterSelect    = document.getElementById('dokter_id');
        const tanggalInput    = document.getElementById('tanggal_kunjungan');
        const jadwalWrapper   = document.getElementById('jadwal-wrapper');
        const jadwalList      = document.getElementById('jadwal-list');
        const jadwalKosong    = document.getElementById('jadwal-kosong');
        const jadwalHidden    = document.getElementById('jadwal_dokter_id');

        function loadJadwal() {
            const dokterId = dokterSelect.value;
            const tanggal  = tanggalInput.value;

            if (!dokterId || !tanggal) {
                jadwalWrapper.classList.add('hidden');
                jadwalKosong.classList.add('hidden');
                return;
            }

            fetch(`/pasien/antrean/jadwal?dokter_id=${dokterId}&tanggal=${tanggal}`)
                .then(r => r.json())
                .then(data => {
                    jadwalList.innerHTML = '';
                    jadwalHidden.value   = '';

                    if (data.length === 0) {
                        jadwalWrapper.classList.add('hidden');
                        jadwalKosong.classList.remove('hidden');
                        return;
                    }

                    jadwalKosong.classList.add('hidden');
                    jadwalWrapper.classList.remove('hidden');

                    data.forEach(j => {
                        const disabled = j.sisa_kuota <= 0;
                        const label    = document.createElement('label');
                        label.className = `flex items-center justify-between p-3 border rounded-lg cursor-pointer transition
                            ${disabled ? 'opacity-50 cursor-not-allowed bg-gray-50' : 'hover:border-emerald-400 hover:bg-emerald-50'}`;
                        label.innerHTML = `
                            <div class="flex items-center gap-3">
                                <input type="radio" name="_jadwal_pick" value="${j.id}"
                                       class="text-emerald-600" ${disabled ? 'disabled' : ''}
                                       onchange="jadwalHidden.value = this.value">
                                <div>
                                    <p class="text-sm font-medium text-gray-800">
                                        ${j.hari}, ${j.jam_mulai.substring(0,5)} – ${j.jam_selesai.substring(0,5)}
                                    </p>
                                </div>
                            </div>
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full
                                ${disabled ? 'bg-red-100 text-red-600' : 'bg-emerald-100 text-emerald-700'}">
                                ${disabled ? 'Penuh' : 'Sisa ' + j.sisa_kuota + ' slot'}
                            </span>`;
                        jadwalList.appendChild(label);
                    });
                });
        }

        dokterSelect.addEventListener('change', loadJadwal);
        tanggalInput.addEventListener('change', loadJadwal);
    </script>
</x-app-layout>
