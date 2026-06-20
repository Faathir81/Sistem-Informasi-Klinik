<?php

namespace App\Filament\Widgets;

use App\Enums\AntreanStatus;
use App\Enums\PengajuanPasienStatus;
use App\Enums\TransaksiStatus;
use App\Filament\Resources\Antreans\AntreanResource;
use App\Filament\Resources\PengajuanPasiens\PengajuanPasienResource;
use App\Filament\Resources\Transaksis\TransaksiResource;
use App\Models\Antrean;
use App\Models\Pemeriksaan;
use App\Models\PengajuanPasien;
use App\Models\Transaksi;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClinicOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Ringkasan Operasional';

    protected ?string $description = 'Kondisi klinik yang perlu dipantau hari ini.';

    protected function getStats(): array
    {
        $antreanHariIni = Antrean::query()
            ->whereDate('tanggal_kunjungan', today())
            ->count();

        $antreanMenunggu = Antrean::query()
            ->whereDate('tanggal_kunjungan', today())
            ->where('status', AntreanStatus::Menunggu->value)
            ->count();

        $pemeriksaanHariIni = Pemeriksaan::query()
            ->whereDate('tgl_pemeriksaan', today())
            ->count();

        $pengajuanPerluTindakan = PengajuanPasien::query()
            ->whereIn('status', [
                PengajuanPasienStatus::MenungguPembayaran->value,
                PengajuanPasienStatus::PembayaranGagal->value,
                PengajuanPasienStatus::Menunggu->value,
            ])
            ->count();

        $pemasukanHariIni = Transaksi::query()
            ->where('status', TransaksiStatus::Settlement->value)
            ->whereDate('tgl_bayar', today())
            ->sum('amount');

        return [
            Stat::make('Antrean Hari Ini', $antreanHariIni)
                ->description("{$antreanMenunggu} masih menunggu")
                ->descriptionIcon('heroicon-m-queue-list')
                ->color($antreanMenunggu > 0 ? 'warning' : 'success')
                ->url(AntreanResource::getUrl()),
            Stat::make('Pemeriksaan Hari Ini', $pemeriksaanHariIni)
                ->description('Rekam medis yang tercatat')
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color('primary'),
            Stat::make('Pemasukan Hari Ini', 'Rp '.number_format((float) $pemasukanHariIni, 0, ',', '.'))
                ->description('Transaksi berstatus lunas')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->url(TransaksiResource::getUrl()),
            Stat::make('Pengajuan Perlu Tindakan', $pengajuanPerluTindakan)
                ->description('Menunggu atau pembayaran bermasalah')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color($pengajuanPerluTindakan > 0 ? 'warning' : 'success')
                ->url(PengajuanPasienResource::getUrl()),
        ];
    }
}
