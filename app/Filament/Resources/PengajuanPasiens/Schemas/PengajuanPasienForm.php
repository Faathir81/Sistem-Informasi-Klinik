<?php

namespace App\Filament\Resources\PengajuanPasiens\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PengajuanPasienForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Placeholder::make('user_name')
                    ->label('Nama Akun')
                    ->content(fn ($record): string => $record?->user?->name ?? '-'),
                Placeholder::make('user_email')
                    ->label('Email Akun')
                    ->content(fn ($record): string => $record?->user?->email ?? '-'),
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
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'Menunggu' => 'Menunggu',
                        'Disetujui' => 'Disetujui',
                        'Ditolak' => 'Ditolak',
                    ])
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
                Textarea::make('alasan_penolakan')
                    ->label('Alasan Penolakan')
                    ->rows(3)
                    ->columnSpanFull()
                    ->disabled()
                    ->dehydrated(false),
                Placeholder::make('no_rekam_medis')
                    ->label('No. Rekam Medis')
                    ->content(fn ($record): string => $record?->pasien?->no_rekam_medis ?? 'Belum dibuat'),
                Placeholder::make('reviewer_name')
                    ->label('Diverifikasi Oleh')
                    ->content(fn ($record): string => $record?->reviewer?->name ?? 'Belum diverifikasi'),
            ]);
    }
}
