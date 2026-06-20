<?php

namespace App\Filament\Resources\Pasiens;

use App\Filament\Resources\Pasiens\Pages\CreatePasien;
use App\Filament\Resources\Pasiens\Pages\EditPasien;
use App\Filament\Resources\Pasiens\Pages\ListPasiens;
use App\Filament\Resources\Pasiens\Schemas\PasienForm;
use App\Filament\Resources\Pasiens\Tables\PasiensTable;
use App\Models\Pasien;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PasienResource extends Resource
{
    protected static ?string $model = Pasien::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $modelLabel = 'Profil Pasien';

    protected static ?string $pluralModelLabel = 'Profil Pasien';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'nama_pasien';

    public static function getNavigationGroup(): ?string
    {
        return 'Data User';
    }

    public static function form(Schema $schema): Schema
    {
        return PasienForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PasiensTable::configure($table);
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
            'index' => ListPasiens::route('/'),
            'create' => CreatePasien::route('/create'),
            'edit' => EditPasien::route('/{record}/edit'),
        ];
    }
}
