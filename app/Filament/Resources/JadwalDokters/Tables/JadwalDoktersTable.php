<?php

namespace App\Filament\Resources\JadwalDokters\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class JadwalDoktersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('dokter.nama_dokter')
                    ->label('Nama Dokter')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('hari')
                    ->label('Hari')
                    ->sortable(),
                TextColumn::make('jam_mulai')
                    ->label('Jam Mulai')
                    ->time('H:i')
                    ->sortable(),
                TextColumn::make('jam_selesai')
                    ->label('Jam Selesai')
                    ->time('H:i')
                    ->sortable(),
                TextColumn::make('kuota')
                    ->label('Kuota Pasien')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
