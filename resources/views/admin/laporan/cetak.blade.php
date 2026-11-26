<!DOCTYPE html>
<html lang="id">

<head>
    @include('partials.favicon')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan pendapatan - Babarberan Haircuts</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/babarberan.css') }}">
    <link rel="stylesheet" href="{{ asset('css/print.css') }}">
</head>

<body class="print-body">

    <div class="print-actions no-print">
        <button type="button" class="btn btn--primary" onclick="window.print()"><span>Cetak</span></button>
        <a href="{{ route('admin.laporan.index', request()->query()) }}"
            class="btn btn--secondary"><span>Kembali</span></a>
    </div>

    <div class="sheet">
        <div class="sheet__header">
            <x-logo />
            <span>{{ \Illuminate\Support\Str::upper($profil?->nama_usaha ?? 'Babarberan Haircuts') }}</span>
        </div>
        <div class="divider"></div>

        <div class="sheet__title">
            <p>LAPORAN TRANSAKSI</p>
            <h1>Laporan pendapatan.</h1>
        </div>

        <div class="sheet__meta">
            <div><span class="k">PERIODE</span><span class="v">{{ $periodeLong }}</span></div>
            <div><span class="k">LAYANAN</span><span class="v">{{ $layananDipilih ?? 'Semua layanan' }}</span>
            </div>
            <div><span class="k">TANGGAL CETAK</span><span
                    class="v">{{ now()->translatedFormat('d F Y') }}</span></div>
        </div>

        <div class="sheet__summary">
            <div><span class="k">TOTAL PENDAPATAN</span><span class="big">@rp($totalPendapatan)</span></div>
            <div><span class="k">TRANSAKSI</span><span class="big">{{ $totalTransaksi }}</span></div>
            <div><span class="k">UNIT LAYANAN</span><span class="big">{{ $totalUnit }}</span></div>
        </div>

        <div class="sheet__summary">
            @foreach ($perMetode as $m)
                <div>
                    <span class="k">{{ strtoupper($m['label']) }} · {{ $m['jml'] }} TRX</span>
                    <span class="big" style="font-size: 16px;">@rp($m['total'])</span>
                </div>
            @endforeach
        </div>

        <table class="sheet__table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>TANGGAL</th>
                    <th>LAYANAN</th>
                    <th>HARGA</th>
                    <th>JML</th>
                    <th>METODE</th>
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($baris as $b)
                    <tr>
                        <td class="muted">TRX-{{ str_pad($b->transaksi_id, 3, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ \Carbon\Carbon::parse($b->tgl)->translatedFormat('d M Y') }}</td>
                        <td class="name">{{ $b->nama_layanan }}</td>
                        <td>@rp($b->harga)</td>
                        <td>{{ $b->qty }}</td>
                        <td>{{ $labelMetode[$b->metode] ?? $b->metode }}</td>
                        <td class="strong">@rp($b->subtotal)</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="muted">Tidak ada transaksi pada periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="sheet__grand">
            <span>TOTAL / {{ $totalTransaksi }} TRANSAKSI / {{ $totalUnit }} UNIT</span>
            <strong>@rp($totalPendapatan)</strong>
        </div>

        <p class="sheet__note">
            {{ $catatan->implode(' ') }}
            Total adalah penjumlahan harga satuan × jumlah setiap transaksi.
        </p>

        <div class="sheet__footer">
            <div class="divider"></div>
            <span>{{ $profil?->nama_usaha ?? 'Babarberan Haircuts' }}@if ($profil?->pemilik)
                    · {{ $profil->pemilik }}
                @endif · Laporan</span>
        </div>
    </div>

    <script>
        window.addEventListener('load', function() {
            window.print();
        });
    </script>
</body>

</html>
