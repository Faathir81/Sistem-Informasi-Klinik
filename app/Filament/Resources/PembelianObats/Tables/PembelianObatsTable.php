<?php

namespace App\Filament\Resources\PembelianObats\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PembelianObatsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal_pembelian')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('supplier')
                    ->label('Supplier')
                    ->placeholder('-')
                    ->searchable(),
                TextColumn::make('details_count')
                    ->label('Item')
                    ->counts('details')
                    ->alignCenter(),
                TextColumn::make('total_pembelian')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),
            ])
            ->defaultSort('tanggal_pembelian', 'desc')
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
