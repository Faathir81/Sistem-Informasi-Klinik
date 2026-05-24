<?php

namespace App\Filament\Resources\Reseps\Schemas;

use App\Models\Obat;
use App\Models\Pemeriksaan;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class ResepForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('pemeriksaan_id')
                    ->label('Pemeriksaan')
                    ->relationship(
                        'pemeriksaan',
                        'id',
                        fn (Builder $query) => $query->with(['pasien', 'dokter'])->latest('tgl_pemeriksaan')
                    )
                    ->getOptionLabelFromRecordUsing(fn (Pemeriksaan $record): string => "{$record->tgl_pemeriksaan->format('d M Y')} - {$record->pasien->nama_pasien} ({$record->dokter->nama_dokter})")
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('total_harga_obat')
                    ->label('Total Harga Obat')
                    ->prefix('Rp')
                    ->disabled()
                    ->dehydrated(false)
                    ->placeholder('Dihitung otomatis dari detail resep'),
                Select::make('status_ambil')
                    ->label('Status Ambil')
                    ->options([
                        'Belum_Diambil' => 'Belum Diambil',
                        'Sudah_Diambil' => 'Sudah Diambil',
                    ])
                    ->default('Belum_Diambil')
                    ->required(),
                Repeater::make('details')
                    ->label('Detail Obat')
                    ->relationship()
                    ->schema([
                        Select::make('obat_id')
                            ->label('Obat')
                            ->relationship('obat', 'nama_obat')
                            ->getOptionLabelFromRecordUsing(fn (Obat $record): string => "{$record->nama_obat} - stok {$record->stok} {$record->satuan}")
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(fn (?int $state, Get $get, Set $set) => self::updateSubtotal($state, $get, $set))
                            ->required(),
                        TextInput::make('jumlah')
                            ->label('Jumlah')
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->live()
                            ->afterStateUpdated(fn ($state, Get $get, Set $set) => self::updateSubtotal((int) $get('obat_id'), $get, $set))
                            ->required(),
                        TextInput::make('aturan_pakai')
                            ->label('Aturan Pakai')
                            ->placeholder('Contoh: 3 x 1 tablet sehari')
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('sub_total')
                            ->label('Subtotal')
                            ->prefix('Rp')
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(2)
                    ->defaultItems(1)
                    ->addActionLabel('Tambah Obat')
                    ->columnSpanFull()
                    ->required(),
            ]);
    }

    private static function updateSubtotal(?int $obatId, Get $get, Set $set): void
    {
        $obat = $obatId ? Obat::find($obatId) : null;
        $jumlah = max((int) $get('jumlah'), 0);

        $set('sub_total', $obat ? $obat->harga_jual * $jumlah : 0);
    }
}
