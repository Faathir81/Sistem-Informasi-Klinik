<?php

namespace App\Filament\Resources\Pemeriksaans\Pages;

use App\Filament\Resources\Pemeriksaans\PemeriksaanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPemeriksaans extends ListRecords
{
    protected static string $resource = PemeriksaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
