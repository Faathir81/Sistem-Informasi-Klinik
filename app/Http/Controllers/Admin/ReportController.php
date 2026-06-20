<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TransaksiStatus;
use App\Http\Controllers\Controller;
use App\Models\Pemeriksaan;
use App\Models\Pengeluaran;
use App\Models\ResepDetail;
use App\Models\StokObat;
use App\Models\Transaksi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function keuangan(Request $request): Response
    {
        [$startDate, $endDate] = $this->dateRange($request);

        $transaksis = Transaksi::query()
            ->with('pemeriksaan.pasien')
            ->where('status', TransaksiStatus::Settlement->value)
            ->whereBetween('tgl_bayar', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->latest('tgl_bayar')
            ->get();

        $pengeluarans = Pengeluaran::query()
            ->whereBetween('tgl_pengeluaran', [$startDate->toDateString(), $endDate->toDateString()])
            ->latest('tgl_pengeluaran')
            ->get();

        return Pdf::loadView('reports.pdf.keuangan', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'transaksis' => $transaksis,
            'pengeluarans' => $pengeluarans,
            'totalPemasukan' => $transaksis->sum('amount'),
            'totalPengeluaran' => $pengeluarans->sum('jumlah'),
        ])
            ->setPaper('a4')
            ->download('laporan-keuangan-'.$startDate->format('Ymd').'-'.$endDate->format('Ymd').'.pdf');
    }

    public function kunjungan(Request $request): Response
    {
        [$startDate, $endDate] = $this->dateRange($request);

        $pemeriksaans = Pemeriksaan::query()
            ->with(['pasien', 'dokter', 'resep.details', 'tindakanDetails'])
            ->whereBetween('tgl_pemeriksaan', [$startDate->toDateString(), $endDate->toDateString()])
            ->latest('tgl_pemeriksaan')
            ->get();

        return Pdf::loadView('reports.pdf.kunjungan', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'pemeriksaans' => $pemeriksaans,
            'totalKunjungan' => $pemeriksaans->count(),
            'totalKonsultasi' => $pemeriksaans->sum('biaya_konsultasi'),
            'totalObat' => $pemeriksaans->sum(fn (Pemeriksaan $pemeriksaan): float => (float) ($pemeriksaan->resep?->total_harga_obat ?? 0)),
            'totalTindakan' => $pemeriksaans->sum(fn (Pemeriksaan $pemeriksaan): float => $pemeriksaan->totalTindakan()),
        ])
            ->setPaper('a4')
            ->download('laporan-kunjungan-'.$startDate->format('Ymd').'-'.$endDate->format('Ymd').'.pdf');
    }

    public function stokObat(Request $request): Response
    {
        [$startDate, $endDate] = $this->dateRange($request);

        $pemakaian = ResepDetail::query()
            ->selectRaw('obat_id, SUM(jumlah) as total_terpakai, SUM(sub_total) as total_nilai')
            ->whereHas('resep.pemeriksaan', fn ($query) => $query->whereBetween('tgl_pemeriksaan', [$startDate->toDateString(), $endDate->toDateString()]))
            ->groupBy('obat_id')
            ->get()
            ->keyBy('obat_id');

        $obats = StokObat::query()
            ->with('obat')
            ->where('stok', '>', 0)
            ->join('obats', 'stok_obats.obat_id', '=', 'obats.id')
            ->select('stok_obats.*')
            ->orderBy('obats.nama_obat')
            ->orderBy('stok_obats.tgl_kadaluarsa')
            ->get()
            ->map(function (StokObat $stokObat) use ($pemakaian): array {
                $usage = $pemakaian->get($stokObat->obat_id);

                return [
                    'stok' => $stokObat,
                    'obat' => $stokObat->obat,
                    'total_terpakai' => (int) ($usage?->total_terpakai ?? 0),
                    'total_nilai' => (float) ($usage?->total_nilai ?? 0),
                    'nilai_stok' => (float) $stokObat->stok * (float) $stokObat->harga_beli,
                ];
            });

        return Pdf::loadView('reports.pdf.stok-obat', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'obats' => $obats,
            'totalTerpakai' => $obats->sum('total_terpakai'),
            'totalNilaiTerpakai' => $obats->sum('total_nilai'),
            'totalNilaiStok' => $obats->sum('nilai_stok'),
        ])
            ->setPaper('a4')
            ->download('laporan-stok-obat-'.$startDate->format('Ymd').'-'.$endDate->format('Ymd').'.pdf');
    }

    private function dateRange(Request $request): array
    {
        $validated = $request->validate([
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
        ]);

        return [
            Carbon::parse($validated['tanggal_mulai'] ?? now()->startOfMonth()->toDateString()),
            Carbon::parse($validated['tanggal_selesai'] ?? now()->toDateString()),
        ];
    }
}
