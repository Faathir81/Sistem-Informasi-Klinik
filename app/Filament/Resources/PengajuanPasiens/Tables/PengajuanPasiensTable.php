<?php

namespace App\Filament\Resources\PengajuanPasiens\Tables;

use App\Models\PengajuanPasien;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

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
                    ->color(fn (string $state): string => match ($state) {
                        'Menunggu' => 'warning',
                        'Disetujui' => 'success',
                        'Ditolak' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('pasien.no_rekam_medis')
                    ->label('No. RM')
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
                    ->options([
                        'Menunggu' => 'Menunggu',
                        'Disetujui' => 'Disetujui',
                        'Ditolak' => 'Ditolak',
                    ]),
            ])
            ->recordActions([
                Action::make('setujui')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Model $record): bool => $record->status === 'Menunggu')
                    ->requiresConfirmation()
                    ->modalHeading('Setujui pengajuan pasien?')
                    ->modalDescription(fn (PengajuanPasien $record): string => "Nomor rekam medis akan dibuat untuk {$record->nama_pasien}.")
                    ->action(function (PengajuanPasien $record): void {
                        try {
                            $pasien = $record->approve(auth()->user());

                            Notification::make()
                                ->title('Pengajuan disetujui')
                                ->body("No. rekam medis {$pasien->no_rekam_medis} berhasil dibuat.")
                                ->success()
                                ->send();
                        } catch (\Throwable $exception) {
                            Notification::make()
                                ->title('Gagal menyetujui pengajuan')
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('tolak')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Model $record): bool => $record->status === 'Menunggu')
                    ->form([
                        Textarea::make('alasan_penolakan')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->rows(4)
                            ->maxLength(1000),
                    ])
                    ->requiresConfirmation()
                    ->modalHeading('Tolak pengajuan pasien?')
                    ->action(function (PengajuanPasien $record, array $data): void {
                        try {
                            $record->reject(auth()->user(), $data['alasan_penolakan']);

                            Notification::make()
                                ->title('Pengajuan ditolak')
                                ->success()
                                ->send();
                        } catch (\Throwable $exception) {
                            Notification::make()
                                ->title('Gagal menolak pengajuan')
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                EditAction::make()
                    ->label('Detail'),
            ]);
    }
}
