<?php

namespace App\Filament\Resources\StokObats\Tables;

use App\Models\StokObat;
use App\Services\Obat\StokObatExpiryService;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StokObatsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('obat.nama_obat')
                    ->label('Nama Obat')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('batch')
                    ->label('Batch')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('harga_beli')
                    ->label('Harga Beli')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('stok')
                    ->label('Stok')
                    ->badge()
                    ->color(fn (int $state): string => $state < 10 ? 'danger' : 'success')
                    ->sortable(),
                TextColumn::make('tgl_kadaluarsa')
                    ->label('Kadaluarsa')
                    ->date('d M Y')
                    ->color(fn (StokObat $record): string => $record->isExpired() ? 'danger' : 'gray')
                    ->sortable(),
            ])
            ->defaultSort('tgl_kadaluarsa')
            ->filters([
                Filter::make('stok_tersedia')
                    ->label('Stok tersedia')
                    ->query(fn (Builder $query): Builder => $query->where('stok_obats.stok', '>', 0)),
                Filter::make('kadaluarsa')
                    ->label('Sudah kadaluwarsa')
                    ->query(fn (Builder $query): Builder => $query->kadaluarsa()),
            ])
            ->recordActions([
                Action::make('hapus_kadaluarsa')
                    ->label('Hapus Kadaluarsa')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (StokObat $record): bool => $record->stok > 0 && $record->isExpired())
                    ->action(fn (StokObat $record): mixed => app(StokObatExpiryService::class)->removeExpired($record)),
            ]);
    }
}
