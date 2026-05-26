<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('pasien.dashboard') }}" class="clinic-btn-quiet px-2" aria-label="Kembali">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <p class="clinic-kicker">Verifikasi pasien</p>
                <h1 class="mt-1 text-2xl font-black text-[#14342f]">Pengajuan Data Pasien</h1>
            </div>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="clinic-section max-w-5xl">
            <div class="grid gap-6 lg:grid-cols-[0.78fr_1.22fr]">
                <aside class="space-y-4">
                    <div class="clinic-card-solid p-6">
                        <p class="clinic-kicker">Akun login</p>
                        <h2 class="mt-2 text-xl font-black text-[#14342f]">{{ $user->name }}</h2>
                        <div class="mt-5 space-y-3 text-sm">
                            <div>
                                <span class="font-bold text-[#62756f]">Email</span>
                                <p class="mt-1 break-words font-semibold text-[#14342f]">{{ $user->email }}</p>
                            </div>
                            <div>
                                <span class="font-bold text-[#62756f]">Nomor HP akun</span>
                                <p class="mt-1 font-semibold text-[#14342f]">{{ $user->no_hp ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    @if($pengajuan?->status === 'Ditolak')
                        <div class="rounded-lg border border-red-200 bg-red-50 p-5">
                            <p class="font-black text-red-700">Pengajuan sebelumnya ditolak</p>
                            <p class="mt-2 text-sm leading-6 text-red-700">{{ $pengajuan->alasan_penolakan ?: 'Admin belum menuliskan alasan.' }}</p>
                            <p class="mt-3 text-xs font-semibold text-red-600">Perbaiki data pada form ini lalu kirim ulang.</p>
                        </div>
                    @endif
                </aside>

                <section class="clinic-card-solid overflow-hidden">
                    <div class="border-b border-slate-100 bg-white p-6">
                        <p class="clinic-kicker">Data medis resmi</p>
                        <h2 class="mt-2 text-2xl font-black text-[#14342f]">Lengkapi identitas pasien.</h2>
                        <p class="mt-2 text-sm leading-6 text-[#62756f]">Data ini akan diverifikasi admin sebelum nomor rekam medis dibuat.</p>
                    </div>

                    <form action="{{ route('pasien.pengajuan-pasien.store') }}" method="POST" class="space-y-6 p-6">
                        @csrf

                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <label for="nik" class="clinic-label block">NIK</label>
                                <input id="nik" name="nik" type="text" maxlength="16" inputmode="numeric" class="clinic-field mt-2" value="{{ old('nik', $pengajuan?->nik) }}" placeholder="16 digit NIK" required>
                                @error('nik')
                                    <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="nama_pasien" class="clinic-label block">Nama Lengkap Pasien</label>
                                <input id="nama_pasien" name="nama_pasien" type="text" class="clinic-field mt-2" value="{{ old('nama_pasien', $pengajuan?->nama_pasien ?? $user->name) }}" required>
                                @error('nama_pasien')
                                    <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="tgl_lahir" class="clinic-label block">Tanggal Lahir</label>
                                <input id="tgl_lahir" name="tgl_lahir" type="date" class="clinic-field mt-2" value="{{ old('tgl_lahir', $pengajuan?->tgl_lahir?->toDateString()) }}" required>
                                @error('tgl_lahir')
                                    <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="jenis_kelamin" class="clinic-label block">Jenis Kelamin</label>
                                <select id="jenis_kelamin" name="jenis_kelamin" class="clinic-field mt-2" required>
                                    <option value="">Pilih jenis kelamin</option>
                                    @foreach(['Laki-laki', 'Perempuan'] as $jenisKelamin)
                                        <option value="{{ $jenisKelamin }}" @selected(old('jenis_kelamin', $pengajuan?->jenis_kelamin) === $jenisKelamin)>
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
                                <input id="no_hp" name="no_hp" type="tel" class="clinic-field mt-2" value="{{ old('no_hp', $pengajuan?->no_hp ?? $user->no_hp) }}" required>
                                @error('no_hp')
                                    <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="alamat" class="clinic-label block">Alamat Lengkap</label>
                            <textarea id="alamat" name="alamat" rows="4" class="clinic-field mt-2" required>{{ old('alamat', $pengajuan?->alamat) }}</textarea>
                            @error('alamat')
                                <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="catatan_pasien" class="clinic-label block">Catatan Tambahan</label>
                            <textarea id="catatan_pasien" name="catatan_pasien" rows="3" class="clinic-field mt-2" placeholder="Opsional, misalnya koreksi data atau informasi untuk admin.">{{ old('catatan_pasien', $pengajuan?->catatan_pasien) }}</textarea>
                            @error('catatan_pasien')
                                <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex flex-col gap-3 border-t border-slate-100 pt-6 sm:flex-row sm:items-center sm:justify-between">
                            <p class="text-sm font-semibold leading-6 text-[#62756f]">Admin akan membuat nomor rekam medis setelah data disetujui.</p>
                            <button type="submit" class="clinic-btn-primary">
                                Kirim Pengajuan
                            </button>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
