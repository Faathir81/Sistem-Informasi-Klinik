<?php

namespace App\Filament\Resources\Pemeriksaans\Schemas;

use App\Enums\AntreanStatus;
use App\Enums\PaymentStatus;
use App\Models\Antrean;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class PemeriksaanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('antrean_id')
                    ->label('Antrean')
                    ->relationship(
                        'antrean',
                        'kode_antrean',
                        fn (Builder $query) => $query
                            ->with(['pasien', 'dokter'])
                            ->whereIn('status', AntreanStatus::billableValues())
                    )
                    ->getOptionLabelFromRecordUsing(fn (Antrean $record): string => "#{$record->nomor_antrean} - {$record->pasien->nama_pasien} ({$record->dokter->nama_dokter})")
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function (?int $state, Set $set): void {
                        $antrean = Antrean::find($state);

                        if (! $antrean) {
                            return;
                        }

                        $set('pasien_id', $antrean->pasien_id);
                        $set('dokter_id', $antrean->dokter_id);
                        $set('tgl_pemeriksaan', now()->toDateString());
                    })
                    ->required(),
                Select::make('pasien_id')
                    ->label('Pasien')
                    ->relationship('pasien', 'nama_pasien')
                    ->searchable()
                    ->preload()
                    ->disabled()
                    ->dehydrated()
                    ->required(),
                Select::make('dokter_id')
                    ->label('Dokter')
                    ->relationship('dokter', 'nama_dokter')
                    ->searchable()
                    ->preload()
                    ->disabled()
                    ->dehydrated()
                    ->required(),
                DatePicker::make('tgl_pemeriksaan')
                    ->label('Tanggal Pemeriksaan')
                    ->default(now())
                    ->native(false)
                    ->required(),
                Textarea::make('keluhan')
                    ->label('Keluhan')
                    ->rows(3)
                    ->columnSpanFull()
                    ->required(),
                Textarea::make('diagnosa')
                    ->label('Diagnosa')
                    ->rows(3)
                    ->columnSpanFull()
                    ->required(),
                Textarea::make('tindakan')
                    ->label('Tindakan')
                    ->rows(3)
                    ->columnSpanFull(),
                TextInput::make('biaya_konsultasi')
                    ->label('Biaya Konsultasi')
                    ->numeric()
                    ->prefix('Rp')
                    ->minValue(0)
                    ->default(0)
                    ->required(),
                Select::make('status_bayar')
                    ->label('Status Bayar')
                    ->options(PaymentStatus::options())
                    ->default(PaymentStatus::BelumBayar->value)
                    ->required(),
            ]);
    }
}
