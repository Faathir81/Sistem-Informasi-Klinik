<x-filament-panels::page>
    <div style="display: grid; gap: 1.5rem; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
        @foreach ([
            [
                'title' => 'Laporan Pemasukan & Kas',
                'description' => 'Rekap pembayaran lunas dan pengeluaran operasional.',
                'route' => route('admin.reports.keuangan'),
            ],
            [
                'title' => 'Laporan Kunjungan',
                'description' => 'Aktivitas pemeriksaan, diagnosa, dan resep.',
                'route' => route('admin.reports.kunjungan'),
            ],
            [
                'title' => 'Laporan Stok Obat',
                'description' => 'Rekap pemakaian dan nilai sisa stok obat saat ini.',
                'route' => route('admin.reports.stok-obat'),
            ],
        ] as $report)
            <x-filament::section>
                <x-slot name="heading">
                    {{ $report['title'] }}
                </x-slot>
                
                <x-slot name="description">
                    <span style="display: block; min-height: 2.5rem;">
                        {{ $report['description'] }}
                    </span>
                </x-slot>

                <form method="GET" action="{{ $report['route'] }}" target="_blank" style="display: flex; flex-direction: column; gap: 1rem; margin-top: 1rem;">
                    <div>
                        <label style="font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem; display: block;">Tanggal Mulai</label>
                        <x-filament::input.wrapper>
                            <x-filament::input
                                type="date"
                                name="tanggal_mulai"
                                value="{{ now()->startOfMonth()->toDateString() }}"
                            />
                        </x-filament::input.wrapper>
                    </div>

                    <div>
                        <label style="font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem; display: block;">Tanggal Selesai</label>
                        <x-filament::input.wrapper>
                            <x-filament::input
                                type="date"
                                name="tanggal_selesai"
                                value="{{ now()->toDateString() }}"
                            />
                        </x-filament::input.wrapper>
                    </div>

                    <div style="margin-top: 0.5rem;">
                        <x-filament::button type="submit" size="md" style="width: 100%; justify-content: center;">
                            Ekspor PDF
                        </x-filament::button>
                    </div>
                </form>
            </x-filament::section>
        @endforeach
    </div>
</x-filament-panels::page>
