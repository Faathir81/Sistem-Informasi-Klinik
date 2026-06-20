<?php

namespace App\Filament\Resources\StokObats;

use App\Filament\Resources\StokObats\Pages\ListStokObats;
use App\Filament\Resources\StokObats\Tables\StokObatsTable;
use App\Models\StokObat;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StokObatResource extends Resource
{
    protected static ?string $model = StokObat::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBeaker;

    protected static ?string $modelLabel = 'Stok Obat';

    protected static ?string $pluralModelLabel = 'Stok Obat';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return 'Apotek';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return StokObatsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStokObats::route('/'),
        ];
    }
}
