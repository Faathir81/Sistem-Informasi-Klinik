<?php

namespace App\Filament\Resources\Transaksis\Schemas;

use App\Enums\TransaksiStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TransaksiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('order_id')
                ->label('Order ID')
                ->disabled()
                ->dehydrated(false),
            TextInput::make('amount')
                ->label('Nominal')
                ->prefix('Rp')
                ->numeric()
                ->required(),
            Select::make('status')
                ->options(TransaksiStatus::options())
                ->required(),
            TextInput::make('payment_type')
                ->label('Tipe Pembayaran')
                ->maxLength(255),
        ]);
    }
}
