<?php

namespace App\Filament\Resources\Pemeriksaans\Tables;

use App\Enums\PaymentStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PemeriksaansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tgl_pemeriksaan')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('pasien.nama_pasien')
                    ->label('Pasien')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('dokter.nama_dokter')
                    ->label('Dokter')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('diagnosa')
                    ->label('Diagnosa')
                    ->limit(45)
                    ->searchable(),
                TextColumn::make('biaya_konsultasi')
                    ->label('Konsultasi')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('status_bayar')
                    ->label('Bayar')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => str_replace('_', ' ', $state))
                    ->color(fn (string $state): string => $state === PaymentStatus::Lunas->value ? 'success' : 'warning'),
                TextColumn::make('resep.total_harga_obat')
                    ->label('Obat')
                    ->money('IDR')
                    ->placeholder('Belum ada resep'),
            ])
            ->defaultSort('tgl_pemeriksaan', 'desc')
            ->filters([
                SelectFilter::make('status_bayar')
                    ->label('Status Bayar')
                    ->options(PaymentStatus::options()),
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
