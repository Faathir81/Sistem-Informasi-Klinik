<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Slip Gaji</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 12px; }
        .header { border-bottom: 2px solid #111827; padding-bottom: 12px; margin-bottom: 24px; }
        .title { font-size: 22px; font-weight: 700; margin: 0; }
        .subtitle { margin: 4px 0 0; color: #4b5563; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        td, th { padding: 9px 10px; border: 1px solid #e5e7eb; text-align: left; }
        th { background: #f3f4f6; }
        .total { font-size: 16px; font-weight: 700; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <p class="title">Klinik Ar-Ridlo</p>
        <p class="subtitle">Slip Gaji Periode {{ $gaji->bulan_tahun }}</p>
    </div>

    <table>
        <tr>
            <th>Penerima</th>
            <td>{{ $gaji->namaPenerima() }}</td>
        </tr>
        <tr>
            <th>Role</th>
            <td>{{ $gaji->role }}</td>
        </tr>
        <tr>
            <th>Status Bayar</th>
            <td>{{ $gaji->status_bayar }}</td>
        </tr>
        <tr>
            <th>Tanggal Bayar</th>
            <td>{{ $gaji->tgl_bayar?->format('d M Y') ?? '-' }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Komponen</th>
                <th class="right">Nominal</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Gaji Pokok</td>
                <td class="right">Rp {{ number_format($gaji->gaji_pokok, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Tunjangan</td>
                <td class="right">Rp {{ number_format($gaji->tunjangan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Potongan</td>
                <td class="right">Rp {{ number_format($gaji->potongan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="total">Total Diterima</td>
                <td class="right total">Rp {{ number_format($gaji->total_diterima, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
