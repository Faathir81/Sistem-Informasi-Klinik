<?php

namespace App\Filament\Resources\Transaksis\Tables;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class TransaksisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_id')
                    ->label('Order ID')
                    ->fontFamily('mono')
                    ->copyable()
                    ->searchable(),
                TextColumn::make('pemeriksaan.pasien.nama_pasien')
                    ->label('Pasien')
                    ->searchable(),
                TextColumn::make('amount')
                    ->label('Nominal')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'SETTLEMENT' => 'success',
                        'PENDING' => 'warning',
                        'EXPIRE' => 'gray',
                        'CANCEL' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('payment_type')
                    ->label('Tipe')
                    ->placeholder('-'),
                TextColumn::make('tgl_bayar')
                    ->label('Tanggal Bayar')
                    ->dateTime('d M Y H:i')
                    ->placeholder('-')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'PENDING' => 'PENDING',
                        'SETTLEMENT' => 'SETTLEMENT',
                        'EXPIRE' => 'EXPIRE',
                        'CANCEL' => 'CANCEL',
                    ]),
            ])
            ->recordActions([
                Action::make('simulasi_lunas')
                    ->label('Simulasi Lunas')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Model $record): bool => $record->status !== 'SETTLEMENT')
                    ->requiresConfirmation()
                    ->modalHeading('Simulasikan pembayaran sukses?')
                    ->action(fn (Model $record) => $record->markSettled('simulator')),
                EditAction::make(),
            ]);
    }
}
