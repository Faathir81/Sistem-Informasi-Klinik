<?php

namespace App\Filament\Resources\JadwalLiburs\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class JadwalLiburForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('tanggal')
                    ->label('Tanggal Libur')
                    ->native(false)
                    ->required(),
                Select::make('dokter_id')
                    ->label('Dokter')
                    ->relationship('dokter', 'nama_dokter')
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->helperText('Kosongkan jika libur berlaku untuk semua dokter.'),
                TextInput::make('keterangan')
                    ->label('Keterangan')
                    ->placeholder('Contoh: Tanggal merah / cuti dokter')
                    ->maxLength(255),
                Toggle::make('status_aktif')
                    ->label('Aktif')
                    ->default(true)
                    ->required(),
            ]);
    }
}
