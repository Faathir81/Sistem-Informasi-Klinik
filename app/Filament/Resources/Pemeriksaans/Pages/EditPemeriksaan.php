<?php

namespace App\Filament\Resources\Pemeriksaans\Pages;

use App\Filament\Resources\Pemeriksaans\PemeriksaanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPemeriksaan extends EditRecord
{
    protected static string $resource = PemeriksaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
