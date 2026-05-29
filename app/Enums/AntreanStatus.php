<?php

namespace App\Enums;

enum AntreanStatus: string
{
    case Menunggu = 'Menunggu';
    case Dipanggil = 'Dipanggil';
    case Selesai = 'Selesai';
    case Batal = 'Batal';

    public static function activeValues(): array
    {
        return [
            self::Menunggu->value,
            self::Dipanggil->value,
        ];
    }

    public static function billableValues(): array
    {
        return [
            self::Dipanggil->value,
            self::Selesai->value,
        ];
    }

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
            self::Menunggu->value => 'warning',
            self::Dipanggil->value => 'info',
            self::Selesai->value => 'success',
            self::Batal->value => 'danger',
            default => 'gray',
        };
    }
}
