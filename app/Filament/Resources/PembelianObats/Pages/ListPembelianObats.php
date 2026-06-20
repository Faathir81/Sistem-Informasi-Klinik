<?php

namespace App\Filament\Resources\PembelianObats\Pages;

use App\Filament\Resources\PembelianObats\PembelianObatResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPembelianObats extends ListRecords
{
    protected static string $resource = PembelianObatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
