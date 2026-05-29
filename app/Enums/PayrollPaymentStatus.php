<?php

namespace App\Enums;

enum PayrollPaymentStatus: string
{
    case Pending = 'Pending';
    case Lunas = 'Lunas';

    public static function options(): array
    {
        return [
            self::Pending->value => self::Pending->value,
            self::Lunas->value => self::Lunas->value,
        ];
    }
}
