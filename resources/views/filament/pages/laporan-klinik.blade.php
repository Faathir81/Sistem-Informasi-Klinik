<x-filament-panels::page>
    <div class="grid gap-6 lg:grid-cols-3">
        @foreach ([
            [
                'title' => 'Laporan Pemasukan & Pengeluaran Kas',
                'description' => 'Rekap pembayaran pasien yang sudah lunas dan pengeluaran operasional klinik.',
                'route' => route('admin.reports.keuangan'),
                'accent' => 'amber',
            ],
            [
                'title' => 'Laporan Kunjungan Konsultasi',
                'description' => 'Daftar aktivitas pemeriksaan, diagnosa, biaya konsultasi, dan nilai resep.',
                'route' => route('admin.reports.kunjungan'),
                'accent' => 'emerald',
            ],
            [
                'title' => 'Laporan Mutasi Stok Obat',
                'description' => 'Rekap pemakaian obat dari resep dan posisi nilai stok obat saat ini.',
                'route' => route('admin.reports.stok-obat'),
                'accent' => 'sky',
            ],
        ] as $report)
            <form method="GET" action="{{ $report['route'] }}" target="_blank" class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="space-y-2">
                    <h2 class="text-base font-semibold text-gray-950 dark:text-white">{{ $report['title'] }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $report['description'] }}</p>
                </div>

                <div class="mt-5 space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-200" for="tanggal_mulai_{{ $loop->index }}">Tanggal Mulai</label>
                        <input id="tanggal_mulai_{{ $loop->index }}" name="tanggal_mulai" type="date" value="{{ now()->startOfMonth()->toDateString() }}" class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:border-gray-700 dark:bg-gray-950 dark:text-white">
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-200" for="tanggal_selesai_{{ $loop->index }}">Tanggal Selesai</label>
                        <input id="tanggal_selesai_{{ $loop->index }}" name="tanggal_selesai" type="date" value="{{ now()->toDateString() }}" class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:border-gray-700 dark:bg-gray-950 dark:text-white">
                    </div>
                </div>

                <button type="submit" class="mt-6 inline-flex w-full items-center justify-center rounded-lg bg-amber-500 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-amber-600">
                    Ekspor PDF
                </button>
            </form>
        @endforeach
    </div>
</x-filament-panels::page>
