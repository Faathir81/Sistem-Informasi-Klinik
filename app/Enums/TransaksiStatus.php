<?php

namespace App\Enums;

enum TransaksiStatus: string
{
    case Pending = 'PENDING';
    case Settlement = 'SETTLEMENT';
    case Expire = 'EXPIRE';
    case Cancel = 'CANCEL';

    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $status) {
            $options[$status->value] = $status->value;
        }

        return $options;
    }

    public static function badgeColor(string $status): string
    {
        return match ($status) {
            self::Settlement->value => 'success',
            self::Pending->value => 'warning',
            self::Expire->value => 'gray',
            self::Cancel->value => 'danger',
            default => 'gray',
        };
    }
}
