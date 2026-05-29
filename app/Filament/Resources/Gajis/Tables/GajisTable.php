<?php

namespace App\Filament\Resources\Gajis\Tables;

use App\Enums\PayrollPaymentStatus;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class GajisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('bulan_tahun')
                    ->label('Periode')
                    ->sortable(),
                TextColumn::make('role')
                    ->badge(),
                TextColumn::make('nama_penerima')
                    ->label('Penerima')
                    ->state(fn (Model $record): string => $record->namaPenerima()),
                TextColumn::make('total_diterima')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('status_bayar')
                    ->badge()
                    ->color(fn (string $state): string => $state === PayrollPaymentStatus::Lunas->value ? 'success' : 'warning'),
                TextColumn::make('tgl_bayar')
                    ->label('Tanggal Bayar')
                    ->date('d M Y')
                    ->placeholder('-'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('role')
                    ->options([
                        'Dokter' => 'Dokter',
                        'Pegawai' => 'Pegawai',
                    ]),
                SelectFilter::make('status_bayar')
                    ->options(PayrollPaymentStatus::options()),
            ])
            ->recordActions([
                Action::make('bayar')
                    ->label('Bayar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Model $record): bool => $record->status_bayar !== PayrollPaymentStatus::Lunas->value)
                    ->requiresConfirmation()
                    ->action(fn (Model $record) => $record->update([
                        'status_bayar' => PayrollPaymentStatus::Lunas->value,
                        'tgl_bayar' => now()->toDateString(),
                    ])),
                Action::make('slip')
                    ->label('Slip PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->url(fn (Model $record): string => route('admin.gaji.slip', $record))
                    ->openUrlInNewTab(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
