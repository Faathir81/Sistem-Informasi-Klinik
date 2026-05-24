<?php

namespace App\Filament\Widgets;

use App\Models\ResepDetail;
use Filament\Widgets\ChartWidget;

class TopMedicineChart extends ChartWidget
{
    protected ?string $heading = 'Obat Paling Laris';

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
                        '#f59e0b',
                        '#10b981',
                        '#38bdf8',
                        '#f97316',
                        '#64748b',
                        '#84cc16',
                    ],
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
