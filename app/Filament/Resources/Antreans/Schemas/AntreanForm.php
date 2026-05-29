<?php

namespace App\Filament\Resources\Antreans\Schemas;

use App\Enums\AntreanStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AntreanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('pasien_id')
                    ->relationship('pasien', 'id')
                    ->required(),
                Select::make('dokter_id')
                    ->relationship('dokter', 'id')
                    ->required(),
                Select::make('jadwal_dokter_id')
                    ->relationship('jadwalDokter', 'id')
                    ->required(),
                DatePicker::make('tanggal_kunjungan')
                    ->required(),
                TextInput::make('nomor_antrean')
                    ->required()
                    ->numeric(),
                TextInput::make('kode_antrean')
                    ->required(),
                Select::make('status')
                    ->options(AntreanStatus::options())
                    ->default(AntreanStatus::Menunggu->value)
                    ->required(),
            ]);
    }
}
