<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan</title>
    @include('reports.pdf._style')
</head>
<body>
    <div class="kop">
        <h1>Klinik Ar-Ridlo</h1>
        <p>Laporan Pemasukan & Pengeluaran Kas</p>
    </div>

    <div class="meta">
        <h2>Periode {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</h2>
        <p>Dicetak pada {{ now()->format('d M Y H:i') }}</p>
    </div>

    <table class="summary">
        <tr>
            <td><strong>Total Pemasukan</strong><br>Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</td>
            <td><strong>Total Pengeluaran</strong><br>Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
            <td><strong>Saldo Bersih</strong><br>Rp {{ number_format($totalPemasukan - $totalPengeluaran, 0, ',', '.') }}</td>
        </tr>
    </table>

    <h3 class="section-title">Pemasukan Pasien</h3>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Order ID</th>
                <th>Pasien</th>
                <th>Metode</th>
                <th class="right">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transaksis as $transaksi)
                <tr>
                    <td>{{ $transaksi->tgl_bayar?->format('d M Y H:i') ?? '-' }}</td>
                    <td>{{ $transaksi->order_id }}</td>
                    <td>{{ $transaksi->pemeriksaan->pasien->nama_pasien ?? '-' }}</td>
                    <td>{{ $transaksi->payment_type ?? '-' }}</td>
                    <td class="right">Rp {{ number_format($transaksi->amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="center muted">Tidak ada pemasukan pada periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h3 class="section-title">Pengeluaran Operasional</h3>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Deskripsi</th>
                <th>Kategori</th>
                <th class="right">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pengeluarans as $pengeluaran)
                <tr>
                    <td>{{ $pengeluaran->tgl_pengeluaran->format('d M Y') }}</td>
                    <td>{{ $pengeluaran->deskripsi }}</td>
                    <td>{{ str_replace('_', ' ', $pengeluaran->kategori) }}</td>
                    <td class="right">Rp {{ number_format($pengeluaran->jumlah, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="center muted">Tidak ada pengeluaran pada periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h3 class="section-title">Pengeluaran Penggajian</h3>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Penerima</th>
                <th>Role</th>
                <th>Periode (Bulan/Tahun)</th>
                <th class="right">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($penggajians as $gaji)
                <tr>
                    <td>{{ $gaji->tgl_bayar?->format('d M Y') ?? '-' }}</td>
                    <td>{{ $gaji->namaPenerima() }}</td>
                    <td>{{ $gaji->role }}</td>
                    <td>{{ $gaji->bulan_tahun }}</td>
                    <td class="right">Rp {{ number_format($gaji->total_diterima, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="center muted">Tidak ada pengeluaran gaji pada periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
