<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class LaporanKlinik extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentChartBar;

    protected static ?string $navigationLabel = 'Laporan Klinik';

    protected static ?string $title = 'Laporan Klinik';

    protected static ?string $slug = 'laporan-klinik';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.laporan-klinik';

    public static function getNavigationGroup(): ?string
    {
        return 'Laporan';
    }
}
