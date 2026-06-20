<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('pasien.profil.index') }}" class="clinic-btn-quiet px-2" aria-label="Kembali">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <p class="clinic-kicker">Profil pasien</p>
                <h1 class="mt-1 text-2xl font-black text-[#14342f]">Edit Profil Pasien</h1>
            </div>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="clinic-section max-w-4xl">
            <section class="clinic-card-solid overflow-hidden">
                <div class="border-b border-slate-100 bg-white p-6">
                    <p class="clinic-kicker">Data identitas</p>
                    <h2 class="mt-2 text-2xl font-black text-[#14342f]">{{ $pasien->nama_pasien }}</h2>
                    <p class="mt-2 text-sm leading-6 text-[#62756f]">Perubahan profil akan dipakai untuk booking dan data kunjungan berikutnya.</p>
                </div>

                <form action="{{ route('pasien.profil.update', $pasien) }}" method="POST" class="space-y-6 p-6">
                    @csrf
                    @method('PATCH')

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label for="nik" class="clinic-label block">NIK</label>
                            <input id="nik" name="nik" type="text" maxlength="16" inputmode="numeric" class="clinic-field mt-2" value="{{ old('nik', $pasien->nik) }}" required>
                            @error('nik')
                                <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="nama_pasien" class="clinic-label block">Nama Lengkap</label>
                            <input id="nama_pasien" name="nama_pasien" type="text" class="clinic-field mt-2" value="{{ old('nama_pasien', $pasien->nama_pasien) }}" required>
                            @error('nama_pasien')
                                <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="tgl_lahir" class="clinic-label block">Tanggal Lahir</label>
                            <input id="tgl_lahir" name="tgl_lahir" type="date" class="clinic-field mt-2" value="{{ old('tgl_lahir', $pasien->tgl_lahir->toDateString()) }}" required>
                            @error('tgl_lahir')
                                <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="jenis_kelamin" class="clinic-label block">Jenis Kelamin</label>
                            <select id="jenis_kelamin" name="jenis_kelamin" class="clinic-field mt-2" required>
                                @foreach(['Laki-laki', 'Perempuan'] as $jenisKelamin)
                                    <option value="{{ $jenisKelamin }}" @selected(old('jenis_kelamin', $pasien->jenis_kelamin) === $jenisKelamin)>
                                        {{ $jenisKelamin }}
                                    </option>
                                @endforeach
                            </select>
                            @error('jenis_kelamin')
                                <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="no_hp" class="clinic-label block">Nomor HP / WhatsApp</label>
                            <input id="no_hp" name="no_hp" type="tel" class="clinic-field mt-2" value="{{ old('no_hp', $pasien->no_hp) }}" required>
                            @error('no_hp')
                                <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="alamat" class="clinic-label block">Alamat Lengkap</label>
                        <textarea id="alamat" name="alamat" rows="4" class="clinic-field mt-2" required>{{ old('alamat', $pasien->alamat) }}</textarea>
                        @error('alamat')
                            <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-3 border-t border-slate-100 pt-6 sm:flex-row sm:justify-end">
                        <a href="{{ route('pasien.profil.index') }}" class="clinic-btn-secondary w-full sm:w-auto">
                            Batal
                        </a>
                        <button type="submit" class="clinic-btn-primary w-full sm:w-auto">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</x-app-layout>
