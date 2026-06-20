<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Stok Obat</title>
    @include('reports.pdf._style')
</head>
<body>
    <div class="kop">
        <h1>Klinik Ar-Ridlo</h1>
        <p>Laporan Mutasi & Nilai Stok Obat</p>
    </div>

    <div class="meta">
        <h2>Periode {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</h2>
        <p>Dicetak pada {{ now()->format('d M Y H:i') }}</p>
    </div>

    <table class="summary">
        <tr>
            <td><strong>Total Obat Terpakai</strong><br>{{ number_format($totalTerpakai, 0, ',', '.') }}</td>
            <td><strong>Nilai Pemakaian</strong><br>Rp {{ number_format($totalNilaiTerpakai, 0, ',', '.') }}</td>
            <td><strong>Nilai Stok Saat Ini</strong><br>Rp {{ number_format($totalNilaiStok, 0, ',', '.') }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Obat</th>
                <th>Batch</th>
                <th>Satuan</th>
                <th class="right">Harga Beli</th>
                <th class="right">Stok Saat Ini</th>
                <th class="right">Terpakai</th>
                <th class="right">Nilai Pemakaian</th>
                <th class="right">Nilai Stok</th>
                <th>Kadaluarsa</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($obats as $row)
                <tr>
                    <td>{{ $row['obat']->nama_obat }}</td>
                    <td>{{ $row['stok']->batch }}</td>
                    <td>{{ $row['obat']->satuan }}</td>
                    <td class="right">Rp {{ number_format($row['stok']->harga_beli, 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($row['stok']->stok, 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($row['total_terpakai'], 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($row['total_nilai'], 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($row['nilai_stok'], 0, ',', '.') }}</td>
                    <td>{{ $row['stok']->tgl_kadaluarsa?->format('d M Y') ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="9" class="center muted">Belum ada data stok obat.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
