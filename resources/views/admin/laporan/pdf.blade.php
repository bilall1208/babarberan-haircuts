<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Laporan Transaksi</title>
    <style>
        @page {
            margin: 40px 40px 60px 40px;
        }

        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #111111;
            line-height: 1.4;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .k {
            font-size: 8px;
            color: #666666;
        }

        .rule {
            border-top: 1px solid #d1d1d1;
            height: 0;
            margin: 10px 0;
        }

        h1 {
            font-size: 24px;
            margin: 2px 0 0 0;
        }

        .eyebrow {
            font-size: 8px;
            color: #666666;
        }

        .big {
            font-size: 20px;
            font-weight: bold;
            margin-top: 4px;
        }

        .data th {
            background: #e8e8e8;
            text-align: left;
            font-size: 8px;
            padding: 9px 6px;
        }

        .data td {
            border-bottom: 1px solid #d1d1d1;
            padding: 9px 6px;
            font-size: 9px;
        }

        .data tr {
            page-break-inside: avoid;
        }

        .r {
            text-align: right;
        }

        .muted {
            color: #666666;
        }

        .grand td {
            border-top: 1px solid #111111;
            border-bottom: 1px solid #111111;
            padding: 10px 0;
            font-weight: bold;
        }

        .note {
            font-size: 8px;
            color: #666666;
            margin-top: 10px;
        }
    </style>
</head>

<body>

    <table>
        <tr>
            <td>
                @if ($logoPath)
                    <img src="{{ $logoPath }}" style="height: 32px;">
                @endif
            </td>
            <td class="r" style="font-size: 9px; font-weight: bold;">
                {{ \Illuminate\Support\Str::upper($profil?->nama_usaha ?? 'Babarberan Haircuts') }}
            </td>
        </tr>
    </table>
    <div class="rule"></div>

    <div class="eyebrow">LAPORAN TRANSAKSI</div>
    <h1>Laporan pendapatan.</h1>

    <table style="margin-top: 14px;">
        <tr>
            <td style="width: 40%;">
                <div class="k">PERIODE</div>
                <div>{{ $periodeLong }}</div>
            </td>
            <td style="width: 30%;">
                <div class="k">LAYANAN</div>
                <div>{{ $layananDipilih ?? 'Semua layanan' }}</div>
            </td>
            <td style="width: 30%;">
                <div class="k">TANGGAL CETAK</div>
                <div>{{ now()->translatedFormat('d F Y') }}</div>
            </td>
        </tr>
    </table>

    <table style="margin-top: 16px;">
        <tr>
            <td style="width: 40%;">
                <div class="k">TOTAL PENDAPATAN</div>
                <div class="big">@rp($totalPendapatan)</div>
            </td>
            <td style="width: 30%;">
                <div class="k">TRANSAKSI</div>
                <div class="big">{{ $totalTransaksi }}</div>
            </td>
            <td style="width: 30%;">
                <div class="k">UNIT LAYANAN</div>
                <div class="big">{{ $totalUnit }}</div>
            </td>
        </tr>
    </table>

    <table style="margin-top: 14px;">
        <tr>
            @foreach ($perMetode as $m)
                <td style="width: 33%;">
                    <div class="k">{{ strtoupper($m['label']) }} · {{ $m['jml'] }} TRX</div>
                    <div style="font-size: 12px; font-weight: bold;">@rp($m['total'])</div>
                </td>
            @endforeach
        </tr>
    </table>

    <table class="data" style="margin-top: 18px;">
        <thead>
            <tr>
                <th style="width: 11%;">ID</th>
                <th style="width: 13%;">TANGGAL</th>
                <th style="width: 24%;">LAYANAN</th>
                <th style="width: 15%;" class="r">HARGA</th>
                <th style="width: 7%;" class="r">JML</th>
                <th style="width: 10%;">METODE</th>
                <th style="width: 20%;" class="r">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($baris as $b)
                <tr>
                    <td class="muted">TRX-{{ str_pad($b->transaksi_id, 3, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ \Carbon\Carbon::parse($b->tgl)->translatedFormat('d M Y') }}</td>
                    <td><b>{{ $b->nama_layanan }}</b></td>
                    <td class="r">@rp($b->harga)</td>
                    <td class="r">{{ $b->qty }}</td>
                    <td>{{ $labelMetode[$b->metode] ?? $b->metode }}</td>
                    <td class="r"><b>@rp($b->subtotal)</b></td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="muted">Tidak ada transaksi pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="grand" style="margin-top: 12px;">
        <tr>
            <td style="font-size: 9px;">TOTAL / {{ $totalTransaksi }} TRANSAKSI / {{ $totalUnit }} UNIT</td>
            <td class="r" style="font-size: 16px;">@rp($totalPendapatan)</td>
        </tr>
    </table>

    <div class="note">
        {{ $catatan->implode(' ') }}
        Total adalah penjumlahan harga satuan × jumlah setiap transaksi.
    </div>

</body>

</html>
