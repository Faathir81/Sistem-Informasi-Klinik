<?php

namespace App\Filament\Widgets;

use App\Models\Pemeriksaan;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class DailyVisitsChart extends ChartWidget
{
    protected ?string $heading = 'Kunjungan Pasien 14 Hari Terakhir';

    protected string $color = 'info';

    protected function getData(): array
    {
        $days = collect(range(0, 13))
            ->map(fn (int $offset): Carbon => now()->startOfDay()->subDays(13 - $offset));

        $pemeriksaans = Pemeriksaan::query()
            ->whereBetween('tgl_pemeriksaan', [$days->first()->toDateString(), $days->last()->toDateString()])
            ->get()
            ->groupBy(fn (Pemeriksaan $pemeriksaan): string => $pemeriksaan->tgl_pemeriksaan->format('Y-m-d'));

        return [
            'datasets' => [
                [
                    'label' => 'Kunjungan',
                    'data' => $days->map(fn (Carbon $day): int => $pemeriksaans->get($day->format('Y-m-d'), collect())->count())->all(),
                    'backgroundColor' => '#38bdf8',
                    'borderColor' => '#0284c7',
                ],
            ],
            'labels' => $days->map(fn (Carbon $day): string => $day->format('d M'))->all(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
