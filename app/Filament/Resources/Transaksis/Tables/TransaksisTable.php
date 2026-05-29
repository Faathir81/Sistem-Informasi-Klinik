<?php

namespace App\Filament\Resources\Transaksis\Tables;

use App\Enums\TransaksiStatus;
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
                    ->state(fn (Model $record): string => $record->pemeriksaan?->pasien?->nama_pasien ?? $record->pengajuanPasien?->nama_pasien ?? '-')
                    ->searchable(),
                TextColumn::make('jenis_transaksi')
                    ->label('Jenis')
                    ->state(fn (Model $record): string => $record->pengajuan_pasien_id ? 'Pendaftaran' : 'Pemeriksaan')
                    ->badge(),
                TextColumn::make('amount')
                    ->label('Nominal')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => TransaksiStatus::badgeColor($state)),
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
                    ->options(TransaksiStatus::options()),
            ])
            ->recordActions([
                Action::make('simulasi_lunas')
                    ->label('Simulasi Lunas')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Model $record): bool => $record->status !== TransaksiStatus::Settlement->value)
                    ->requiresConfirmation()
                    ->modalHeading('Simulasikan pembayaran sukses?')
                    ->action(fn (Model $record) => $record->markSettled('simulator')),
                EditAction::make(),
            ]);
    }
}
