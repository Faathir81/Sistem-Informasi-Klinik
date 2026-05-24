<?php

namespace App\Filament\Resources\Pasiens\Schemas;

use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PasienForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Akun Pengguna (Pasien)')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->nullable()
                    ->helperText('Opsional. Hubungkan pasien dengan akun login yang sudah ada.'),
                TextInput::make('no_rekam_medis')
                    ->label('No. Rekam Medis')
                    ->disabled()
                    ->dehydrated(false)
                    ->placeholder('Dibuat otomatis saat disimpan')
                    ->helperText('Nomor rekam medis dibuat otomatis oleh sistem (RM-YYYYMMDD-XXXX).'),
                TextInput::make('nik')
                    ->label('NIK (No. Induk Kependudukan)')
                    ->required()
                    ->maxLength(16),
                TextInput::make('nama_pasien')
                    ->label('Nama Lengkap Pasien')
                    ->required(),
                DatePicker::make('tgl_lahir')
                    ->label('Tanggal Lahir')
                    ->required()
                    ->native(false),
                Select::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->options(['Laki-laki' => 'Laki-laki', 'Perempuan' => 'Perempuan'])
                    ->required(),
                TextInput::make('no_hp')
                    ->label('Nomor HP/WhatsApp')
                    ->tel()
                    ->required(),
                Textarea::make('alamat')
                    ->label('Alamat Lengkap')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
