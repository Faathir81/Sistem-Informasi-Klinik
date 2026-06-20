<?php

namespace App\Filament\Resources\JadwalLiburs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class JadwalLibursTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('dokter.nama_dokter')
                    ->label('Dokter')
                    ->placeholder('Semua dokter')
                    ->searchable(),
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->searchable(),
                IconColumn::make('status_aktif')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->defaultSort('tanggal', 'desc')
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
