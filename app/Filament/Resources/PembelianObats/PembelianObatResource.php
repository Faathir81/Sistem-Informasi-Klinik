<?php

namespace App\Filament\Resources\PembelianObats;

use App\Filament\Resources\PembelianObats\Pages\CreatePembelianObat;
use App\Filament\Resources\PembelianObats\Pages\EditPembelianObat;
use App\Filament\Resources\PembelianObats\Pages\ListPembelianObats;
use App\Filament\Resources\PembelianObats\Schemas\PembelianObatForm;
use App\Filament\Resources\PembelianObats\Tables\PembelianObatsTable;
use App\Models\PembelianObat;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PembelianObatResource extends Resource
{
    protected static ?string $model = PembelianObat::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedReceiptRefund;

    protected static ?string $modelLabel = 'Pembelian Obat';

    protected static ?string $pluralModelLabel = 'Pembelian Obat';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return 'Apotek';
    }

    public static function form(Schema $schema): Schema
    {
        return PembelianObatForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PembelianObatsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPembelianObats::route('/'),
            'create' => CreatePembelianObat::route('/create'),
            'edit' => EditPembelianObat::route('/{record}/edit'),
        ];
    }
}
