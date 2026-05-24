<?php

namespace App\Filament\Resources\Antreans\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class AntreansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nomor_antrean')
                    ->label('No.')
                    ->sortable()
                    ->alignCenter()
                    ->weight('bold')
                    ->size('lg'),
                TextColumn::make('pasien.nama_pasien')
                    ->label('Nama Pasien')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('dokter.nama_dokter')
                    ->label('Dokter')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tanggal_kunjungan')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('jadwalDokter.jam_mulai')
                    ->label('Jam')
                    ->formatStateUsing(fn ($record) => substr($record->jadwalDokter->jam_mulai, 0, 5) . ' – ' . substr($record->jadwalDokter->jam_selesai, 0, 5)),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Menunggu'  => 'warning',
                        'Dipanggil' => 'info',
                        'Selesai'   => 'success',
                        'Batal'     => 'danger',
                        default     => 'gray',
                    }),
                TextColumn::make('kode_antrean')
                    ->label('Kode QR')
                    ->fontFamily('mono')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('tanggal_kunjungan', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Antrean')
                    ->options([
                        'Menunggu'  => 'Menunggu',
                        'Dipanggil' => 'Dipanggil',
                        'Selesai'   => 'Selesai',
                        'Batal'     => 'Batal',
                    ]),
                SelectFilter::make('tanggal_kunjungan')
                    ->label('Hari Ini')
                    ->query(fn ($query) => $query->whereDate('tanggal_kunjungan', today()))
                    ->label('Hanya Hari Ini'),
            ])
            ->recordActions([
                // Tombol PANGGIL — hanya muncul jika status Menunggu
                Action::make('panggil')
                    ->label('📢 Panggil')
                    ->color('info')
                    ->icon('heroicon-o-megaphone')
                    ->visible(fn (Model $record) => $record->status === 'Menunggu')
                    ->requiresConfirmation()
                    ->modalHeading('Panggil Pasien?')
                    ->modalDescription(fn (Model $record) => "Panggil nomor #{$record->nomor_antrean} - {$record->pasien->nama_pasien}?")
                    ->action(fn (Model $record) => $record->update(['status' => 'Dipanggil'])),

                // Tombol SELESAI — hanya muncul jika status Dipanggil
                Action::make('selesai')
                    ->label('✅ Selesai')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn (Model $record) => $record->status === 'Dipanggil')
                    ->requiresConfirmation()
                    ->modalHeading('Tandai Selesai?')
                    ->action(fn (Model $record) => $record->update(['status' => 'Selesai'])),

                // Tombol BATAL — hanya muncul jika status Menunggu
                Action::make('batal')
                    ->label('Batalkan')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn (Model $record) => $record->status === 'Menunggu')
                    ->requiresConfirmation()
                    ->modalHeading('Batalkan Antrean?')
                    ->action(fn (Model $record) => $record->update(['status' => 'Batal'])),

                EditAction::make()->label('Edit'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
