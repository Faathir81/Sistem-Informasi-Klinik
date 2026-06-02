<?php

namespace App\Filament\Resources\Gajis\Schemas;

use App\Enums\PayrollPaymentStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class GajiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('role')
                ->options([
                    'Dokter' => 'Dokter',
                    'Pegawai' => 'Pegawai',
                ])
                ->live()
                ->required(),
            Select::make('dokter_id')
                ->label('Dokter')
                ->relationship('dokter', 'nama_dokter')
                ->searchable()
                ->preload()
                ->visible(fn (Get $get): bool => $get('role') === 'Dokter'),
            Select::make('pegawai_id')
                ->label('Pegawai')
                ->relationship('pegawai', 'nama_pegawai')
                ->searchable()
                ->preload()
                ->visible(fn (Get $get): bool => $get('role') === 'Pegawai'),
            TextInput::make('bulan_tahun')
                ->label('Periode Gaji')
                ->type('month')
                ->placeholder('2026-05')
                ->required()
                ->maxLength(7),
            TextInput::make('gaji_pokok')
                ->label('Gaji Pokok')
                ->prefix('Rp')
                ->numeric()
                ->minValue(0)
                ->required(),
            TextInput::make('tunjangan')
                ->prefix('Rp')
                ->numeric()
                ->minValue(0)
                ->default(0)
                ->required(),
            TextInput::make('potongan')
                ->prefix('Rp')
                ->numeric()
                ->minValue(0)
                ->default(0)
                ->required(),
            Select::make('status_bayar')
                ->label('Status Bayar')
                ->options(PayrollPaymentStatus::options())
                ->default(PayrollPaymentStatus::Pending->value)
                ->required(),
            DatePicker::make('tgl_bayar')
                ->label('Tanggal Bayar')
                ->native(false),
        ]);
    }
}
