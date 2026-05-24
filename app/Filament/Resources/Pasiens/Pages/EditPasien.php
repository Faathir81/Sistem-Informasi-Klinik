<?php

namespace App\Filament\Resources\Pasiens\Pages;

use App\Filament\Resources\Pasiens\PasienResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPasien extends EditRecord
{
    protected static string $resource = PasienResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
