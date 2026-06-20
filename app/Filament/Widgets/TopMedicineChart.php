<?php

namespace App\Filament\Widgets;

use App\Models\ResepDetail;
use Filament\Widgets\ChartWidget;

class TopMedicineChart extends ChartWidget
{
    protected static ?int $sort = 5;

    protected ?string $heading = 'Obat Paling Laris';

    protected ?string $description = 'Enam obat dengan pemakaian tertinggi.';

    protected ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $items = ResepDetail::query()
            ->with('obat')
            ->get()
            ->groupBy('obat_id')
            ->map(fn ($details) => [
                'nama_obat' => $details->first()->obat?->nama_obat ?? 'Tidak diketahui',
                'jumlah' => $details->sum('jumlah'),
            ])
            ->sortByDesc('jumlah')
            ->take(6)
            ->values();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Terpakai',
                    'data' => $items->pluck('jumlah')->all(),
                    'backgroundColor' => [
                        '#14342f',
                        '#ef7b2d',
                        '#7ba891',
                        '#62756f',
                        '#c75f1d',
                        '#3a695b',
                    ],
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $items->pluck('nama_obat')->all(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
