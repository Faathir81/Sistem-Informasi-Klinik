<?php

namespace App\Filament\Resources\Pengeluarans\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PengeluaranForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('deskripsi')
                ->label('Deskripsi')
                ->required()
                ->maxLength(255),
            TextInput::make('jumlah')
                ->label('Jumlah')
                ->prefix('Rp')
                ->numeric()
                ->minValue(0)
                ->required(),
            Select::make('kategori')
                ->options([
                    'Operasional' => 'Operasional',
                    'Pembelian_Obat' => 'Pembelian Obat',
                    'Lain_Lain' => 'Lain-lain',
                ])
                ->required(),
            DatePicker::make('tgl_pengeluaran')
                ->label('Tanggal Pengeluaran')
                ->default(now())
                ->native(false)
                ->required(),
        ]);
    }
}
