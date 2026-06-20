<?php

namespace App\Filament\Widgets;

use App\Models\Pemeriksaan;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class DailyVisitsChart extends ChartWidget
{
    protected static ?int $sort = 4;

    protected ?string $heading = 'Kunjungan Pasien 14 Hari Terakhir';

    protected ?string $description = 'Pergerakan volume pemeriksaan harian.';

    protected ?string $maxHeight = '280px';

    protected string $color = 'warning';

    public ?string $filter = '14';

    protected function getFilters(): ?array
    {
        return [
            '7' => '7 Hari Terakhir',
            '14' => '14 Hari Terakhir',
            '30' => '30 Hari Terakhir',
        ];
    }

    protected function getData(): array
    {
        $daysCount = (int) $this->filter;

        $days = collect(range(0, $daysCount - 1))
            ->map(fn (int $offset): Carbon => now()->startOfDay()->subDays(($daysCount - 1) - $offset));

        $pemeriksaans = Pemeriksaan::query()
            ->whereBetween('tgl_pemeriksaan', [$days->first()->toDateString(), $days->last()->toDateString()])
            ->get()
            ->groupBy(fn (Pemeriksaan $pemeriksaan): string => $pemeriksaan->tgl_pemeriksaan->format('Y-m-d'));

        return [
            'datasets' => [
                [
                    'label' => 'Kunjungan',
                    'data' => $days->map(fn (Carbon $day): int => $pemeriksaans->get($day->format('Y-m-d'), collect())->count())->all(),
                    'backgroundColor' => '#ef7b2d', // Oranye tema
                    'borderColor' => '#c75f1d',
                    'borderRadius' => 4,
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
