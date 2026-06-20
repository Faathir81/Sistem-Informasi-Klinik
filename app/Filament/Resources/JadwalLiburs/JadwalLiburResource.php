<?php

namespace App\Filament\Resources\JadwalLiburs;

use App\Filament\Resources\JadwalLiburs\Pages\CreateJadwalLibur;
use App\Filament\Resources\JadwalLiburs\Pages\EditJadwalLibur;
use App\Filament\Resources\JadwalLiburs\Pages\ListJadwalLiburs;
use App\Filament\Resources\JadwalLiburs\Schemas\JadwalLiburForm;
use App\Filament\Resources\JadwalLiburs\Tables\JadwalLibursTable;
use App\Models\JadwalLibur;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class JadwalLiburResource extends Resource
{
    protected static ?string $model = JadwalLibur::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $modelLabel = 'Jadwal Libur';

    protected static ?string $pluralModelLabel = 'Jadwal Libur';

    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): ?string
    {
        return 'Jadwal & SDM';
    }

    public static function form(Schema $schema): Schema
    {
        return JadwalLiburForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JadwalLibursTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListJadwalLiburs::route('/'),
            'create' => CreateJadwalLibur::route('/create'),
            'edit' => EditJadwalLibur::route('/{record}/edit'),
        ];
    }
}
