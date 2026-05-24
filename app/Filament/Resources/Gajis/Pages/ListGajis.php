<?php

namespace App\Filament\Resources\Gajis\Pages;

use App\Filament\Resources\Gajis\GajiResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGajis extends ListRecords
{
    protected static string $resource = GajiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
