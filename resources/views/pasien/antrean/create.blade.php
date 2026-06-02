<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('pasien.antrean.index') }}" class="clinic-btn-quiet px-2" aria-label="Kembali">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <p class="clinic-kicker">Antrean</p>
                <h1 class="mt-1 text-2xl font-black text-[#14342f]">Booking Antrean</h1>
            </div>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="clinic-section max-w-5xl">
            <div class="grid gap-6 lg:grid-cols-[0.78fr_1.22fr]">
                <aside class="space-y-4">
                    <div class="clinic-card-solid p-6">
                        <p class="clinic-kicker">Data pasien</p>
                        <h2 class="mt-2 text-xl font-black text-[#14342f]">{{ $pasien->nama_pasien }}</h2>
                        <div class="mt-5 space-y-3 text-sm">
                            <div>
                                <span class="font-bold text-[#62756f]">No. Rekam Medis</span>
                                <p class="mt-1 font-mono font-bold text-[#14342f]">{{ $pasien->no_rekam_medis }}</p>
                            </div>
                            <div>
                                <span class="font-bold text-[#62756f]">Nomor HP</span>
                                <p class="mt-1 font-semibold text-[#14342f]">{{ $pasien->no_hp }}</p>
                            </div>
                        </div>
                    </div>

                    @if($antreanHariIni)
                        <div class="rounded-lg border border-amber-200 bg-amber-50 p-5">
                            <p class="font-black text-amber-800">Anda sudah memiliki antrean aktif hari ini</p>
                            <p class="mt-2 text-sm leading-6 text-amber-700">
                                Nomor {{ str_pad($antreanHariIni->nomor_antrean, 3, '0', STR_PAD_LEFT) }} dengan {{ $antreanHariIni->dokter->nama_dokter }}.
                            </p>
                            <x-status-badge class="mt-3" type="antrean" :value="$antreanHariIni->status" />
                            <a href="{{ route('pasien.antrean.tiket', $antreanHariIni->kode_antrean) }}" class="mt-4 inline-flex rounded-md border border-amber-300 px-4 py-2 text-sm font-bold text-amber-800 transition hover:bg-amber-100">
                                Lihat Tiket
                            </a>
                        </div>
                    @endif
                </aside>

                <section class="clinic-card-solid overflow-hidden">
                    <div class="border-b border-slate-100 bg-white p-6">
                        <p class="clinic-kicker">Form booking</p>
                        <h2 class="mt-2 text-2xl font-black text-[#14342f]">Pilih tanggal, dokter, dan jadwal.</h2>
                        <p class="mt-2 text-sm leading-6 text-[#62756f]">Sistem hanya menampilkan jadwal dokter yang sesuai dengan hari kunjungan dan sisa kuota.</p>
                    </div>

                    @if(session('error'))
                        <div class="mx-6 mt-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('pasien.antrean.store') }}" method="POST" class="space-y-6 p-6" id="form-booking" data-schedule-url="{{ route('pasien.antrean.jadwal') }}">
                        @csrf

                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <label for="tanggal_kunjungan" class="clinic-label block">Tanggal Kunjungan</label>
                                <input type="date"
                                       id="tanggal_kunjungan"
                                       name="tanggal_kunjungan"
                                       min="{{ today()->toDateString() }}"
                                       value="{{ old('tanggal_kunjungan', today()->toDateString()) }}"
                                       class="clinic-field mt-2"
                                       required>
                                @error('tanggal_kunjungan')
                                    <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="dokter_id" class="clinic-label block">Dokter</label>
                                <select id="dokter_id" name="dokter_id" class="clinic-field mt-2" required>
                                    <option value="">Pilih dokter</option>
                                    @foreach($dokters as $dokter)
                                        <option value="{{ $dokter->id }}" {{ old('dokter_id') == $dokter->id ? 'selected' : '' }}>
                                            {{ $dokter->nama_dokter }} ({{ $dokter->spesialisasi }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('dokter_id')
                                    <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div id="jadwal-wrapper" class="hidden">
                            <div class="mb-3 flex items-center justify-between gap-3">
                                <label class="clinic-label block">Jadwal Tersedia</label>
                                <span class="text-xs font-bold text-[#62756f]">Pilih satu slot</span>
                            </div>
                            <div id="jadwal-list" class="grid gap-3"></div>
                            <input type="hidden" id="jadwal_dokter_id" name="jadwal_dokter_id">
                            @error('jadwal_dokter_id')
                                <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="jadwal-kosong" class="hidden rounded-lg border border-orange-200 bg-orange-50 px-4 py-3 text-sm font-semibold text-orange-700">
                            Tidak ada jadwal praktek tersedia untuk dokter ini pada tanggal yang dipilih.
                        </div>

                        <button type="submit" id="btn-submit-booking" class="clinic-btn-primary w-full disabled:cursor-not-allowed disabled:opacity-50">
                            Ambil Nomor Antrean
                        </button>
                    </form>
                </section>
            </div>
        </div>
    </div>

</x-app-layout>
