<?php

namespace App\Filament\Resources\Pengeluarans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PengeluaransTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tgl_pengeluaran')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('deskripsi')
                    ->searchable(),
                TextColumn::make('kategori')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => str_replace('_', ' ', $state)),
                TextColumn::make('jumlah')
                    ->money('IDR')
                    ->sortable(),
            ])
            ->defaultSort('tgl_pengeluaran', 'desc')
            ->filters([
                SelectFilter::make('kategori')
                    ->options([
                        'Operasional' => 'Operasional',
                        'Pembelian_Obat' => 'Pembelian Obat',
                        'Lain_Lain' => 'Lain-lain',
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
