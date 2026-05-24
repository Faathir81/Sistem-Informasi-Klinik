<?php

namespace App\Filament\Resources\Dokters\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DokterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_dokter')
                    ->required(),
                TextInput::make('spesialisasi')
                    ->required(),
                TextInput::make('no_hp')
                    ->required(),
                Toggle::make('status_aktif')
                    ->required(),
            ]);
    }
}
