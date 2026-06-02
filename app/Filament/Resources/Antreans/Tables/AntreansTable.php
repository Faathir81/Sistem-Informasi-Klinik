<?php

namespace App\Filament\Resources\Antreans\Tables;

use App\Enums\AntreanStatus;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

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
                    ->formatStateUsing(fn ($record): string => $record->jadwalDokter
                        ? substr($record->jadwalDokter->jam_mulai, 0, 5).' - '.substr($record->jadwalDokter->jam_selesai, 0, 5)
                        : '-'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => AntreanStatus::badgeColor($state)),
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
                    ->options(AntreanStatus::options()),
                Filter::make('hari_ini')
                    ->label('Hanya Hari Ini')
                    ->query(fn (Builder $query): Builder => $query->whereDate('tanggal_kunjungan', now(config('app.timezone'))->toDateString())),
                Filter::make('tanggal_spesifik')
                    ->label('Tanggal Kunjungan')
                    ->schema([
                        DatePicker::make('tanggal')
                            ->label('Tanggal')
                            ->native(false),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['tanggal'] ?? null, fn (Builder $query, string $tanggal): Builder => $query->whereDate('tanggal_kunjungan', $tanggal))),
            ])
            ->recordActions([
                Action::make('panggil')
                    ->label('📢 Panggil')
                    ->color('info')
                    ->icon('heroicon-o-megaphone')
                    ->visible(fn (Model $record): bool => $record->status === AntreanStatus::Menunggu->value && self::canCallQueue($record))
                    ->requiresConfirmation()
                    ->modalHeading('Panggil Pasien?')
                    ->modalDescription(fn (Model $record) => "Panggil nomor #{$record->nomor_antrean} - {$record->pasien->nama_pasien}?")
                    ->action(fn (Model $record) => $record->update(['status' => AntreanStatus::Dipanggil->value])),

                Action::make('selesai')
                    ->label('✅ Selesai')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn (Model $record) => $record->status === AntreanStatus::Dipanggil->value)
                    ->requiresConfirmation()
                    ->modalHeading('Tandai Selesai?')
                    ->action(fn (Model $record) => $record->update(['status' => AntreanStatus::Selesai->value])),

                Action::make('batal')
                    ->label('Batalkan')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn (Model $record) => $record->status === AntreanStatus::Menunggu->value)
                    ->requiresConfirmation()
                    ->modalHeading('Batalkan Antrean?')
                    ->action(fn (Model $record) => $record->update(['status' => AntreanStatus::Batal->value])),

                EditAction::make()->label('Edit'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    private static function canCallQueue(Model $record): bool
    {
        if (! $record->tanggal_kunjungan?->isSameDay(now(config('app.timezone')))) {
            return false;
        }

        if (! $record->jadwalDokter) {
            return false;
        }

        $tanggal = $record->tanggal_kunjungan->toDateString();
        $timezone = config('app.timezone', 'Asia/Jakarta');
        $now = now($timezone);
        $jamMulai = Carbon::parse($tanggal.' '.$record->jadwalDokter->jam_mulai, $timezone);
        $jamSelesai = Carbon::parse($tanggal.' '.$record->jadwalDokter->jam_selesai, $timezone);

        return $now->between($jamMulai, $jamSelesai);
    }
}
