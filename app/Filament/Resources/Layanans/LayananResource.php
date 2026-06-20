<?php

namespace App\Filament\Resources\Layanans;

use App\Filament\Resources\Layanans\Pages\CreateLayanan;
use App\Filament\Resources\Layanans\Pages\EditLayanan;
use App\Filament\Resources\Layanans\Pages\ListLayanans;
use App\Filament\Resources\Layanans\Schemas\LayananForm;
use App\Filament\Resources\Layanans\Tables\LayanansTable;
use App\Models\Layanan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LayananResource extends Resource
{
    protected static ?string $model = Layanan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static ?string $modelLabel = 'Layanan Klinik';

    protected static ?string $pluralModelLabel = 'Tindakan & Layanan';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'nama_layanan';

    public static function getNavigationGroup(): ?string
    {
        return 'Pelayanan Medis';
    }

    public static function form(Schema $schema): Schema
    {
        return LayananForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LayanansTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLayanans::route('/'),
            'create' => CreateLayanan::route('/create'),
            'edit' => EditLayanan::route('/{record}/edit'),
        ];
    }
}
