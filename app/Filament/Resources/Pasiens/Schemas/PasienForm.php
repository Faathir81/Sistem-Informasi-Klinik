<?php

namespace App\Filament\Resources\Pasiens\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PasienForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas Pasien')
                    ->description('Data utama yang digunakan pada antrean dan rekam medis.')
                    ->schema([
                        TextInput::make('nik')
                            ->label('NIK')
                            ->helperText('Nomor Induk Kependudukan, 16 digit.')
                            ->required()
                            ->maxLength(16),
                        TextInput::make('nama_pasien')
                            ->label('Nama Lengkap')
                            ->required(),
                        DatePicker::make('tgl_lahir')
                            ->label('Tanggal Lahir')
                            ->required()
                            ->native(false),
                        Select::make('jenis_kelamin')
                            ->label('Jenis Kelamin')
                            ->options(['Laki-laki' => 'Laki-laki', 'Perempuan' => 'Perempuan'])
                            ->required(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Kontak & Akun')
                    ->description('Informasi komunikasi dan akses portal pasien.')
                    ->schema([
                        TextInput::make('no_hp')
                            ->label('Nomor HP / WhatsApp')
                            ->tel()
                            ->required(),
                        Select::make('user_id')
                            ->label('Akun Pengguna')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->nullable()
                            ->helperText('Opsional. Hubungkan dengan akun login yang sudah ada.'),
                        Textarea::make('alamat')
                            ->label('Alamat Lengkap')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
