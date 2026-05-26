<?php

namespace App\Filament\Resources\PengajuanPasiens;

use App\Filament\Resources\PengajuanPasiens\Pages\EditPengajuanPasien;
use App\Filament\Resources\PengajuanPasiens\Pages\ListPengajuanPasiens;
use App\Filament\Resources\PengajuanPasiens\Schemas\PengajuanPasienForm;
use App\Filament\Resources\PengajuanPasiens\Tables\PengajuanPasiensTable;
use App\Models\PengajuanPasien;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PengajuanPasienResource extends Resource
{
    protected static ?string $model = PengajuanPasien::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $modelLabel = 'Pengajuan Pasien';

    protected static ?string $pluralModelLabel = 'Pengajuan Pasien';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'nama_pasien';

    public static function getNavigationGroup(): ?string
    {
        return 'Data Master Klinik';
    }

    public static function getNavigationBadge(): ?string
    {
        $count = PengajuanPasien::where('status', 'Menunggu')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function form(Schema $schema): Schema
    {
        return PengajuanPasienForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PengajuanPasiensTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPengajuanPasiens::route('/'),
            'edit' => EditPengajuanPasien::route('/{record}/edit'),
        ];
    }
}
