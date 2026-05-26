<?php

namespace App\Filament\Resources\PengajuanPasiens\Pages;

use App\Filament\Resources\PengajuanPasiens\PengajuanPasienResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPengajuanPasien extends EditRecord
{
    protected static string $resource = PengajuanPasienResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
