<?php

namespace App\Filament\Resources\JadwalLiburs\Pages;

use App\Filament\Resources\JadwalLiburs\JadwalLiburResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListJadwalLiburs extends ListRecords
{
    protected static string $resource = JadwalLiburResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
