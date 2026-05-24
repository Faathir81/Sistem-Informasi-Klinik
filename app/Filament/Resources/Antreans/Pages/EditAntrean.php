<?php

namespace App\Filament\Resources\Antreans\Pages;

use App\Filament\Resources\Antreans\AntreanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAntrean extends EditRecord
{
    protected static string $resource = AntreanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
