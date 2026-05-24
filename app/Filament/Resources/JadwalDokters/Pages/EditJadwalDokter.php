<?php

namespace App\Filament\Resources\JadwalDokters\Pages;

use App\Filament\Resources\JadwalDokters\JadwalDokterResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditJadwalDokter extends EditRecord
{
    protected static string $resource = JadwalDokterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
