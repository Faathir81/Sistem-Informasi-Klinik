<?php

namespace App\Enums;

enum PengajuanPasienStatus: string
{
    case MenungguPembayaran = 'Menunggu Pembayaran';
    case Disetujui = 'Disetujui';
    case PembayaranGagal = 'Pembayaran Gagal';

    // Legacy values kept for existing rows during transition.
    case Menunggu = 'Menunggu';
    case Ditolak = 'Ditolak';

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
            self::MenungguPembayaran->value, self::Menunggu->value => 'warning',
            self::Disetujui->value => 'success',
            self::PembayaranGagal->value, self::Ditolak->value => 'danger',
            default => 'gray',
        };
    }
}
