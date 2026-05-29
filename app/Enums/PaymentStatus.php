<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case BelumBayar = 'Belum_Bayar';
    case Lunas = 'Lunas';

    public static function options(): array
    {
        return [
            self::BelumBayar->value => 'Belum Bayar',
            self::Lunas->value => self::Lunas->value,
        ];
    }
}
