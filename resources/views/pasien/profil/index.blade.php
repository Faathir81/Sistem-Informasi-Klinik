<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="clinic-kicker">Profil pasien</p>
                <h1 class="mt-1 text-2xl font-black text-[#14342f]">Kelola Profil Keluarga</h1>
            </div>
            <a href="{{ route('pasien.pengajuan-pasien.create') }}" class="clinic-btn-primary w-full sm:w-auto">
                Tambah Profil
            </a>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="clinic-section max-w-5xl space-y-5">
            @if(session('success'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            <section class="clinic-card-solid p-6">
                <p class="clinic-kicker">Akun keluarga</p>
                <h2 class="mt-2 text-2xl font-black text-[#14342f]">Satu akun dapat mengelola beberapa profil pasien.</h2>
                <p class="mt-2 text-sm leading-6 text-[#62756f]">Profil yang sudah memiliki antrean atau riwayat pemeriksaan tidak bisa dihapus agar data klinik tetap utuh.</p>
            </section>

            @forelse($pasiens as $pasien)
                @php
                    $canDelete = $pasien->antreans_count === 0 && $pasien->pemeriksaans_count === 0;
                @endphp

                <article class="clinic-card-solid overflow-hidden">
                    <div class="grid gap-5 p-6 lg:grid-cols-[1fr_auto] lg:items-start">
                        <div>
                            <p class="clinic-kicker">{{ $pasien->jenis_kelamin }}</p>
                            <h2 class="mt-2 text-2xl font-black text-[#14342f]">{{ $pasien->nama_pasien }}</h2>
                            <div class="mt-4 grid gap-3 text-sm md:grid-cols-3">
                                <div class="rounded-lg bg-[#f3faf6] p-4">
                                    <span class="font-bold text-[#62756f]">Tanggal lahir</span>
                                    <p class="mt-1 font-semibold text-[#14342f]">{{ $pasien->tgl_lahir->format('d M Y') }}</p>
                                </div>
                                <div class="rounded-lg bg-[#f3faf6] p-4">
                                    <span class="font-bold text-[#62756f]">Nomor HP</span>
                                    <p class="mt-1 font-semibold text-[#14342f]">{{ $pasien->no_hp }}</p>
                                </div>
                                <div class="rounded-lg bg-[#f3faf6] p-4">
                                    <span class="font-bold text-[#62756f]">Riwayat</span>
                                    <p class="mt-1 font-semibold text-[#14342f]">{{ $pasien->antreans_count }} antrean, {{ $pasien->pemeriksaans_count }} pemeriksaan</p>
                                </div>
                            </div>
                            <p class="mt-4 text-sm font-semibold leading-6 text-[#62756f]">{{ $pasien->alamat }}</p>
                        </div>

                        <div class="flex flex-col gap-2 sm:flex-row lg:flex-col lg:items-stretch">
                            <a href="{{ route('pasien.profil.edit', $pasien) }}" class="clinic-btn-secondary min-h-10 w-full px-4 py-2">
                                Edit
                            </a>

                            @if($canDelete)
                                <form action="{{ route('pasien.profil.destroy', $pasien) }}" method="POST" onsubmit="return confirm('Hapus profil pasien ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex min-h-10 w-full items-center justify-center rounded-md border border-red-200 px-4 py-2 text-sm font-bold text-red-600 transition hover:bg-red-50">
                                        Hapus
                                    </button>
                                </form>
                            @else
                                <span class="inline-flex min-h-10 w-full items-center justify-center rounded-md bg-slate-100 px-4 py-2 text-center text-sm font-bold text-[#62756f]">
                                    Tidak bisa dihapus
                                </span>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="clinic-card-solid p-10 text-center">
                    <h2 class="text-xl font-black text-[#14342f]">Belum ada profil pasien</h2>
                    <p class="mt-2 text-sm leading-6 text-[#62756f]">Tambahkan profil pasien untuk mulai booking antrean.</p>
                    <a href="{{ route('pasien.pengajuan-pasien.create') }}" class="clinic-btn-primary mt-6">
                        Tambah Profil
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
