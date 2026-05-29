<?php

namespace App\Filament\Resources\Pasiens\Pages;

use App\Filament\Resources\Pasiens\PasienResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreatePasien extends CreateRecord
{
    protected static string $resource = PasienResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(fn (): Model => static::getModel()::create($data));
    }
}
