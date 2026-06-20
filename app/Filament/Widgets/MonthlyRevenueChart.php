<?php

namespace App\Filament\Widgets;

use App\Enums\TransaksiStatus;
use App\Models\Transaksi;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class MonthlyRevenueChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected ?string $heading = 'Tren Pemasukan Bulanan';

    protected ?string $description = 'Nilai transaksi yang sudah diselesaikan.';

    protected ?string $maxHeight = '300px';

    protected string $color = 'primary';

    public ?string $filter = '12';

    protected function getFilters(): ?array
    {
        return [
            '3' => '3 Bulan Terakhir',
            '6' => '6 Bulan Terakhir',
            '12' => '12 Bulan Terakhir',
            '24' => '24 Bulan Terakhir',
        ];
    }

    protected function getData(): array
    {
        $monthsCount = (int) $this->filter;

        $months = collect(range(0, $monthsCount - 1))
            ->map(fn (int $offset): Carbon => now()->startOfMonth()->subMonths(($monthsCount - 1) - $offset));

        $transaksis = Transaksi::query()
            ->where('status', TransaksiStatus::Settlement->value)
            ->whereBetween('tgl_bayar', [$months->first()->copy()->startOfMonth(), $months->last()->copy()->endOfMonth()])
            ->get()
            ->groupBy(fn (Transaksi $transaksi): string => $transaksi->tgl_bayar?->format('Y-m') ?? '');

        return [
            'datasets' => [
                [
                    'label' => 'Pemasukan',
                    'data' => $months->map(fn (Carbon $month): float => $this->sumAmount($transaksis->get($month->format('Y-m'), collect())))->all(),
                    'borderColor' => '#14342f', // Hijau gelap
                    'backgroundColor' => 'rgba(20, 52, 47, 0.15)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $months->map(fn (Carbon $month): string => $month->format('M Y'))->all(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    private function sumAmount(Collection $transaksis): float
    {
        return $transaksis->sum(fn (Transaksi $transaksi): float => (float) $transaksi->amount);
    }
}
