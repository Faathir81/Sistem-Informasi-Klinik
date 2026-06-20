<?php

namespace App\Filament\Resources\Dokters;

use App\Filament\Resources\Dokters\Pages\CreateDokter;
use App\Filament\Resources\Dokters\Pages\EditDokter;
use App\Filament\Resources\Dokters\Pages\ListDokters;
use App\Filament\Resources\Dokters\Schemas\DokterForm;
use App\Filament\Resources\Dokters\Tables\DoktersTable;
use App\Models\Dokter;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DokterResource extends Resource
{
    protected static ?string $model = Dokter::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    protected static ?string $modelLabel = 'Dokter';

    protected static ?string $pluralModelLabel = 'Dokter';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'nama_dokter';

    public static function getNavigationGroup(): ?string
    {
        return 'Jadwal & SDM';
    }

    public static function form(Schema $schema): Schema
    {
        return DokterForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DoktersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDokters::route('/'),
            'create' => CreateDokter::route('/create'),
            'edit' => EditDokter::route('/{record}/edit'),
        ];
    }
}
