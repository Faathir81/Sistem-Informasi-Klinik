<?php

namespace App\Filament\Resources\Pegawais\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PegawaiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_pegawai')
                    ->required(),
                TextInput::make('jabatan')
                    ->required(),
                TextInput::make('no_hp')
                    ->required(),
            ]);
    }
}
