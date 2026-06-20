<?php

namespace App\Filament\Resources\Antreans;

use App\Filament\Resources\Antreans\Pages\CreateAntrean;
use App\Filament\Resources\Antreans\Pages\EditAntrean;
use App\Filament\Resources\Antreans\Pages\ListAntreans;
use App\Filament\Resources\Antreans\Schemas\AntreanForm;
use App\Filament\Resources\Antreans\Tables\AntreansTable;
use App\Models\Antrean;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AntreanResource extends Resource
{
    protected static ?string $model = Antrean::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQueueList;

    protected static ?string $modelLabel = 'Antrean';

    protected static ?string $pluralModelLabel = 'Antrean';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'kode_antrean';

    public static function getNavigationGroup(): ?string
    {
        return 'Operasional';
    }

    public static function form(Schema $schema): Schema
    {
        return AntreanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AntreansTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['pasien', 'dokter', 'jadwalDokter']);
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
            'index' => ListAntreans::route('/'),
            'create' => CreateAntrean::route('/create'),
            'edit' => EditAntrean::route('/{record}/edit'),
        ];
    }
}
