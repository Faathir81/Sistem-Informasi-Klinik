<?php

namespace App\Filament\Resources\PembelianObats\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class PembelianObatForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pembelian')
                    ->description('Data pemasok dan ringkasan transaksi pembelian obat.')
                    ->schema([
                        DatePicker::make('tanggal_pembelian')
                            ->label('Tanggal Pembelian')
                            ->default(now())
                            ->native(false)
                            ->required(),
                        TextInput::make('supplier')
                            ->label('Supplier')
                            ->maxLength(255),
                        TextInput::make('total_pembelian')
                            ->label('Total Pembelian')
                            ->prefix('Rp')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Dihitung otomatis'),
                        Textarea::make('catatan')
                            ->label('Catatan')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
                Section::make('Item Pembelian')
                    ->description('Batch pembelian yang akan menambah stok inventaris.')
                    ->schema([
                        Repeater::make('details')
                            ->label('Detail Pembelian')
                            ->relationship()
                            ->schema([
                                Select::make('obat_id')
                                    ->label('Obat')
                                    ->relationship('obat', 'nama_obat')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                TextInput::make('batch')
                                    ->label('Batch')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('harga_beli')
                                    ->label('Harga Beli')
                                    ->prefix('Rp')
                                    ->numeric()
                                    ->minValue(0)
                                    ->live()
                                    ->afterStateUpdated(fn ($state, Get $get, Set $set) => self::updateSubtotal($get, $set))
                                    ->required(),
                                TextInput::make('jumlah')
                                    ->label('Jumlah')
                                    ->numeric()
                                    ->minValue(1)
                                    ->default(1)
                                    ->live()
                                    ->afterStateUpdated(fn ($state, Get $get, Set $set) => self::updateSubtotal($get, $set))
                                    ->required(),
                                DatePicker::make('tgl_kadaluarsa')
                                    ->label('Tanggal Kadaluarsa')
                                    ->native(false)
                                    ->required(),
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
                    ])
                    ->columnSpanFull(),
            ]);
    }

    private static function updateSubtotal(Get $get, Set $set): void
    {
        $set('sub_total', (float) $get('harga_beli') * (int) $get('jumlah'));
    }
}
