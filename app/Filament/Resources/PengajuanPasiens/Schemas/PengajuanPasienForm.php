<?php

namespace App\Filament\Resources\PengajuanPasiens\Schemas;

use App\Enums\PengajuanPasienStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PengajuanPasienForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Akun Pengaju')
                    ->schema([
                        Placeholder::make('user_name')
                            ->label('Nama Akun')
                            ->content(fn ($record): string => $record?->user?->name ?? '-'),
                        Placeholder::make('user_email')
                            ->label('Email Akun')
                            ->content(fn ($record): string => $record?->user?->email ?? '-'),
                        Select::make('status')
                            ->label('Status Pengajuan')
                            ->options(PengajuanPasienStatus::options())
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
                Section::make('Profil Pasien')
                    ->schema([
                        TextInput::make('nik')
                            ->label('NIK')
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('nama_pasien')
                            ->label('Nama Pasien')
                            ->disabled()
                            ->dehydrated(false),
                        DatePicker::make('tgl_lahir')
                            ->label('Tanggal Lahir')
                            ->native(false)
                            ->disabled()
                            ->dehydrated(false),
                        Select::make('jenis_kelamin')
                            ->label('Jenis Kelamin')
                            ->options(['Laki-laki' => 'Laki-laki', 'Perempuan' => 'Perempuan'])
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('no_hp')
                            ->label('Nomor HP')
                            ->disabled()
                            ->dehydrated(false),
                        Textarea::make('alamat')
                            ->label('Alamat')
                            ->rows(3)
                            ->columnSpanFull()
                            ->disabled()
                            ->dehydrated(false),
                        Textarea::make('catatan_pasien')
                            ->label('Catatan Pasien')
                            ->rows(3)
                            ->columnSpanFull()
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Pembayaran Pendaftaran')
                    ->schema([
                        Placeholder::make('transaction_order')
                            ->label('Order ID')
                            ->content(fn ($record): string => $record?->transaksi?->order_id ?? '-'),
                        Placeholder::make('transaction_status')
                            ->label('Status Pembayaran')
                            ->content(fn ($record): string => $record?->transaksi?->status ?? '-'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
