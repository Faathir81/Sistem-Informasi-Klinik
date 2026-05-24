<?php

namespace App\Filament\Resources\Gajis\Pages;

use App\Filament\Resources\Gajis\GajiResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGaji extends EditRecord
{
    protected static string $resource = GajiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
