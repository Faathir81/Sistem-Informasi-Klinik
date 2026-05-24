<?php

namespace App\Filament\Resources\Pemeriksaans;

use App\Filament\Resources\Pemeriksaans\Pages\CreatePemeriksaan;
use App\Filament\Resources\Pemeriksaans\Pages\EditPemeriksaan;
use App\Filament\Resources\Pemeriksaans\Pages\ListPemeriksaans;
use App\Filament\Resources\Pemeriksaans\Schemas\PemeriksaanForm;
use App\Filament\Resources\Pemeriksaans\Tables\PemeriksaansTable;
use App\Models\Pemeriksaan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PemeriksaanResource extends Resource
{
    protected static ?string $model = Pemeriksaan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $modelLabel = 'Pemeriksaan';

    protected static ?string $pluralModelLabel = 'Rekam Medis';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return 'Medis & Apotek';
    }

    public static function form(Schema $schema): Schema
    {
        return PemeriksaanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PemeriksaansTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPemeriksaans::route('/'),
            'create' => CreatePemeriksaan::route('/create'),
            'edit' => EditPemeriksaan::route('/{record}/edit'),
        ];
    }
}
