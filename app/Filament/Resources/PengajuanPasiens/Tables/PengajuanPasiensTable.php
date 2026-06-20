<?php

namespace App\Filament\Resources\PengajuanPasiens\Tables;

use App\Enums\PengajuanPasienStatus;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PengajuanPasiensTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('nama_pasien')
                    ->label('Nama Pasien')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label('Akun')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('nik')
                    ->label('NIK')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('no_hp')
                    ->label('No. HP')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => PengajuanPasienStatus::badgeColor($state)),
                TextColumn::make('transaksi.status')
                    ->label('Pembayaran')
                    ->badge()
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('reviewed_at')
                    ->label('Diverifikasi')
                    ->dateTime('d M Y H:i')
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(PengajuanPasienStatus::options()),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Detail'),
            ]);
    }
}
