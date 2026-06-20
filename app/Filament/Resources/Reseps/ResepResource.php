<?php

namespace App\Filament\Resources\Reseps;

use App\Filament\Resources\Reseps\Pages\CreateResep;
use App\Filament\Resources\Reseps\Pages\EditResep;
use App\Filament\Resources\Reseps\Pages\ListReseps;
use App\Filament\Resources\Reseps\Schemas\ResepForm;
use App\Filament\Resources\Reseps\Tables\ResepsTable;
use App\Models\Resep;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ResepResource extends Resource
{
    protected static ?string $model = Resep::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $modelLabel = 'Resep';

    protected static ?string $pluralModelLabel = 'Resep';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return 'Pelayanan Medis';
    }

    public static function form(Schema $schema): Schema
    {
        return ResepForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ResepsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReseps::route('/'),
            'create' => CreateResep::route('/create'),
            'edit' => EditResep::route('/{record}/edit'),
        ];
    }
}
