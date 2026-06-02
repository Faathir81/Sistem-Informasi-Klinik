<?php

namespace App\Filament\Resources\Layanans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class LayananForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_layanan')
                    ->label('Nama Layanan')
                    ->placeholder('Contoh: Urut keseleo')
                    ->required()
                    ->maxLength(255),
                TextInput::make('tarif_default')
                    ->label('Tarif Default')
                    ->prefix('Rp')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->required(),
                Toggle::make('status_aktif')
                    ->label('Aktif')
                    ->default(true)
                    ->required(),
            ]);
    }
}
