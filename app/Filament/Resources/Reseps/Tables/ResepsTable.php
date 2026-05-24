<?php

namespace App\Filament\Resources\Reseps\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ResepsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pemeriksaan.tgl_pemeriksaan')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('pemeriksaan.pasien.nama_pasien')
                    ->label('Pasien')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('pemeriksaan.dokter.nama_dokter')
                    ->label('Dokter')
                    ->searchable(),
                TextColumn::make('details_count')
                    ->label('Item Obat')
                    ->counts('details')
                    ->alignCenter(),
                TextColumn::make('total_harga_obat')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('status_ambil')
                    ->label('Status Ambil')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => str_replace('_', ' ', $state))
                    ->color(fn (string $state): string => $state === 'Sudah_Diambil' ? 'success' : 'warning'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status_ambil')
                    ->label('Status Ambil')
                    ->options([
                        'Belum_Diambil' => 'Belum Diambil',
                        'Sudah_Diambil' => 'Sudah Diambil',
                    ]),
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
