<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Tiket Antrean {{ $antrean->kode_antrean }}</title>
    @php
        $qrSvg = QrCode::size(210)->margin(1)->format('svg')->generate($antrean->kode_antrean);
        $qrDataUri = 'data:image/svg+xml;base64,'.base64_encode($qrSvg);
    @endphp
    <style>
        @font-face {
            font-family: 'Figtree';
            font-style: normal;
            font-weight: 400;
            src: url('{{ public_path('fonts/figtree/figtree-latin-400-normal.woff') }}') format('woff');
        }

        @font-face {
            font-family: 'Figtree';
            font-style: normal;
            font-weight: 600;
            src: url('{{ public_path('fonts/figtree/figtree-latin-600-normal.woff') }}') format('woff');
        }

        @font-face {
            font-family: 'Figtree';
            font-style: normal;
            font-weight: 700;
            src: url('{{ public_path('fonts/figtree/figtree-latin-700-normal.woff') }}') format('woff');
        }

        @font-face {
            font-family: 'Figtree';
            font-style: normal;
            font-weight: 900;
            src: url('{{ public_path('fonts/figtree/figtree-latin-900-normal.woff') }}') format('woff');
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #14342f;
            font-family: Figtree, DejaVu Sans, Arial, sans-serif;
            font-size: 13px;
            line-height: 1.5;
        }

        .ticket {
            border: 1px solid #d6e7dd;
            border-radius: 12px;
            overflow: hidden;
        }

        .header {
            background: #14342f;
            color: #ffffff;
            padding: 24px 28px;
        }

        .kicker {
            color: #f8b37d;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.8px;
            text-transform: uppercase;
        }

        h1 {
            margin: 6px 0 0;
            font-family: Figtree, DejaVu Sans, Arial, sans-serif;
            font-size: 28px;
            font-weight: 900;
            line-height: 1.1;
        }

        .date {
            margin-top: 8px;
            color: #d7e7e2;
            font-weight: 700;
        }

        .content {
            padding: 28px;
        }

        .queue-box {
            width: 210px;
            border: 1px solid #d6e7dd;
            border-radius: 10px;
            background: #f3faf6;
            padding: 18px;
            text-align: center;
            vertical-align: top;
        }

        .queue-number {
            margin: 10px 0;
            font-family: Figtree, DejaVu Sans, Arial, sans-serif;
            font-size: 72px;
            font-weight: 900;
            line-height: 1;
        }

        .code {
            color: #62756f;
            font-family: Figtree, DejaVu Sans Mono, monospace;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 2px;
        }

        .details {
            padding-left: 22px;
            vertical-align: top;
        }

        .detail-grid {
            width: 100%;
            border-collapse: collapse;
        }

        .detail-grid td {
            width: 50%;
            padding: 0 10px 12px 0;
        }

        .detail-box {
            min-height: 68px;
            border-radius: 8px;
            background: #f8fafc;
            padding: 12px;
        }

        .label {
            display: block;
            color: #62756f;
            font-family: Figtree, DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
            font-weight: 700;
        }

        .value {
            display: block;
            margin-top: 4px;
            font-family: Figtree, DejaVu Sans, Arial, sans-serif;
            font-size: 14px;
            font-weight: 700;
        }

        .patient {
            margin-top: 2px;
            border: 1px solid #d6e7dd;
            border-radius: 8px;
            padding: 12px;
        }

        .qr-section {
            margin-top: 28px;
            padding-top: 24px;
            border-top: 1px solid #d6e7dd;
            text-align: center;
        }

        .qr-title {
            margin: 4px 0 8px;
            font-family: Figtree, DejaVu Sans, Arial, sans-serif;
            font-size: 21px;
            font-weight: 900;
        }

        .qr-box {
            display: inline-block;
            margin-top: 14px;
            border: 1px solid #d6e7dd;
            border-radius: 10px;
            padding: 14px;
        }

        .qr-image {
            display: block;
            width: 210px;
            height: 210px;
        }
    </style>
</head>
<body>
    <main class="ticket">
        <section class="header">
            <div class="kicker">Klinik Ar-Ridlo</div>
            <h1>Tiket Antrean</h1>
            <div class="date">{{ $antrean->tanggal_kunjungan->isoFormat('dddd, D MMMM Y') }}</div>
        </section>

        <section class="content">
            <table style="width: 100%;">
                <tr>
                    <td class="queue-box">
                        <span class="label">Nomor Antrean</span>
                        <div class="queue-number">{{ str_pad($antrean->nomor_antrean, 3, '0', STR_PAD_LEFT) }}</div>
                        <div class="code">{{ $antrean->kode_antrean }}</div>
                    </td>
                    <td class="details">
                        <table class="detail-grid">
                            <tr>
                                <td>
                                    <div class="detail-box">
                                        <span class="label">Dokter</span>
                                        <span class="value">{{ $antrean->dokter->nama_dokter }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="detail-box">
                                        <span class="label">Spesialisasi</span>
                                        <span class="value">{{ $antrean->dokter->spesialisasi }}</span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="detail-box">
                                        <span class="label">Jam Praktek</span>
                                        <span class="value">{{ substr($antrean->jadwalDokter->jam_mulai, 0, 5) }} - {{ substr($antrean->jadwalDokter->jam_selesai, 0, 5) }} WIB</span>
                                    </div>
                                </td>
                            </tr>
                        </table>

                        <div class="patient">
                            <span class="label">Pasien</span>
                            <span class="value">{{ $antrean->pasien->nama_pasien }}</span>
                        </div>
                    </td>
                </tr>
            </table>

            <section class="qr-section">
                <div class="kicker">QR Code</div>
                <div class="qr-title">Tunjukkan kepada petugas</div>
                <div>QR ini memuat kode antrean unik untuk verifikasi kunjungan.</div>
                <div class="qr-box">
                    <img class="qr-image" src="{{ $qrDataUri }}" alt="QR Code {{ $antrean->kode_antrean }}">
                </div>
            </section>
        </section>
    </main>
</body>
</html>
