<?php

namespace App\Filament\Widgets;

use App\Models\Obat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ApotekAlertWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Kondisi Apotek';

    protected ?string $description = 'Peringatan inventaris yang memerlukan perhatian.';

    protected function getStats(): array
    {
        $stokKritis = Obat::stokKritis()->count();
        $kadaluarsaSegera = Obat::kadaluarsaSegera()->count();
        $totalObat = Obat::count();

        return [
            Stat::make('Stok Kritis', $stokKritis)
                ->description('Obat dengan stok di bawah 10')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($stokKritis > 0 ? 'danger' : 'success'),
            Stat::make('Kadaluarsa <= 30 Hari', $kadaluarsaSegera)
                ->description('Termasuk obat yang sudah lewat tanggal')
                ->descriptionIcon('heroicon-m-clock')
                ->color($kadaluarsaSegera > 0 ? 'warning' : 'success'),
            Stat::make('Total Jenis Obat', $totalObat)
                ->description('Inventaris aktif di apotek')
                ->descriptionIcon('heroicon-m-beaker')
                ->color('info'),
        ];
    }
}
