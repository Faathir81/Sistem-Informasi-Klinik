<?php

namespace App\Filament\Widgets;

use App\Models\Transaksi;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class MonthlyRevenueChart extends ChartWidget
{
    protected ?string $heading = 'Tren Pemasukan Bulanan';

    protected string $color = 'success';

    protected function getData(): array
    {
        $months = collect(range(0, 11))
            ->map(fn (int $offset): Carbon => now()->startOfMonth()->subMonths(11 - $offset));

        $transaksis = Transaksi::query()
            ->where('status', 'SETTLEMENT')
            ->whereBetween('tgl_bayar', [$months->first()->copy()->startOfMonth(), $months->last()->copy()->endOfMonth()])
            ->get()
            ->groupBy(fn (Transaksi $transaksi): string => $transaksi->tgl_bayar?->format('Y-m') ?? '');

        return [
            'datasets' => [
                [
                    'label' => 'Pemasukan',
                    'data' => $months->map(fn (Carbon $month): float => $this->sumAmount($transaksis->get($month->format('Y-m'), collect())))->all(),
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.16)',
                    'fill' => true,
                    'tension' => 0.35,
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
