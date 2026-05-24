<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Kunjungan</title>
    @include('reports.pdf._style')
</head>
<body>
    <div class="kop">
        <h1>Klinik Ar-Ridlo</h1>
        <p>Laporan Aktivitas Kunjungan Konsultasi Pasien</p>
    </div>

    <div class="meta">
        <h2>Periode {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</h2>
        <p>Dicetak pada {{ now()->format('d M Y H:i') }}</p>
    </div>

    <table class="summary">
        <tr>
            <td><strong>Total Kunjungan</strong><br>{{ number_format($totalKunjungan, 0, ',', '.') }}</td>
            <td><strong>Total Konsultasi</strong><br>Rp {{ number_format($totalKonsultasi, 0, ',', '.') }}</td>
            <td><strong>Total Obat</strong><br>Rp {{ number_format($totalObat, 0, ',', '.') }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Pasien</th>
                <th>Dokter</th>
                <th>Diagnosa</th>
                <th class="right">Konsultasi</th>
                <th class="right">Obat</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pemeriksaans as $pemeriksaan)
                <tr>
                    <td>{{ $pemeriksaan->tgl_pemeriksaan->format('d M Y') }}</td>
                    <td>{{ $pemeriksaan->pasien->nama_pasien }}</td>
                    <td>{{ $pemeriksaan->dokter->nama_dokter }}</td>
                    <td>{{ $pemeriksaan->diagnosa }}</td>
                    <td class="right">Rp {{ number_format($pemeriksaan->biaya_konsultasi, 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($pemeriksaan->resep?->total_harga_obat ?? 0, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="center muted">Tidak ada kunjungan pada periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
