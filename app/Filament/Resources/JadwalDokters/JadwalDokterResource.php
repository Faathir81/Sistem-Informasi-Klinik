<?php

namespace App\Filament\Resources\JadwalDokters;

use App\Filament\Resources\JadwalDokters\Pages\CreateJadwalDokter;
use App\Filament\Resources\JadwalDokters\Pages\EditJadwalDokter;
use App\Filament\Resources\JadwalDokters\Pages\ListJadwalDokters;
use App\Filament\Resources\JadwalDokters\Schemas\JadwalDokterForm;
use App\Filament\Resources\JadwalDokters\Tables\JadwalDoktersTable;
use App\Models\JadwalDokter;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class JadwalDokterResource extends Resource
{
    protected static ?string $model = JadwalDokter::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $modelLabel = 'Jadwal Praktek';

    protected static ?string $pluralModelLabel = 'Jadwal Praktek Dokter';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'hari';

    public static function getNavigationGroup(): ?string
    {
        return 'Data Master Klinik';
    }

    public static function form(Schema $schema): Schema
    {
        return JadwalDokterForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JadwalDoktersTable::configure($table);
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
            'index' => ListJadwalDokters::route('/'),
            'create' => CreateJadwalDokter::route('/create'),
            'edit' => EditJadwalDokter::route('/{record}/edit'),
        ];
    }
}
