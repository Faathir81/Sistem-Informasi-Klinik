<?php

namespace App\Filament\Resources\Obats\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ObatForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_obat')
                    ->label('Nama Obat')
                    ->required()
                    ->maxLength(255),
                TextInput::make('satuan')
                    ->label('Satuan')
                    ->placeholder('Tablet, kapsul, botol, strip')
                    ->required()
                    ->maxLength(100),
                TextInput::make('stok')
                    ->label('Stok')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->required(),
                TextInput::make('harga_beli')
                    ->label('Harga Beli')
                    ->numeric()
                    ->prefix('Rp')
                    ->minValue(0)
                    ->default(0)
                    ->required(),
                TextInput::make('harga_jual')
                    ->label('Harga Jual')
                    ->numeric()
                    ->prefix('Rp')
                    ->minValue(0)
                    ->default(0)
                    ->required(),
                DatePicker::make('tgl_kadaluarsa')
                    ->label('Tanggal Kadaluarsa')
                    ->native(false),
            ]);
    }
}
