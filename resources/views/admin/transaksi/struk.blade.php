<!DOCTYPE html>
<html lang="id">

<head>
    @include('partials.favicon')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk {{ $transaksi->kode_tampil }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/babarberan.css') }}">
    <link rel="stylesheet" href="{{ asset('css/struk.css') }}">
</head>

<body class="struk-body">

    <div class="struk-actions no-print">
        <button type="button" class="btn btn--primary" onclick="window.print()"><span>Cetak struk</span></button>
        <a href="{{ route('admin.transaksi.show', $transaksi) }}" class="btn btn--secondary"><span>Kembali</span></a>
    </div>

    <div class="struk">
        <div class="struk__brand">
            <x-logo />
            <p class="struk__name">{{ $profil?->nama_usaha ?? 'Babarberan Haircuts' }}</p>
            @if ($profil?->alamat)
                <p class="struk__meta">{{ $profil->alamat }}</p>
            @endif
            @if ($profil?->whatsapp)
                <p class="struk__meta">WA {{ $profil->whatsapp }}</p>
            @endif
        </div>

        <div class="struk__rule"></div>

        <div class="struk__rows">
            <div class="struk__row"><span>No.</span><span>{{ $transaksi->kode_tampil }}</span></div>
            <div class="struk__row"><span>Tanggal</span><span>{{ $transaksi->tanggal->translatedFormat('d M Y') }}
                    {{ $transaksi->created_at->format('H:i') }}</span></div>
            <div class="struk__row"><span>Kasir</span><span>{{ $transaksi->user?->name ?? '-' }}</span></div>
        </div>

        <div class="struk__rule"></div>

        @foreach ($transaksi->detail as $d)
            <div class="struk__item">
                <p class="struk__item-name">{{ $d->nama_layanan }}</p>
                <div class="struk__row">
                    <span>{{ $d->qty }} × @rp($d->harga)</span>
                    <span>@rp($d->subtotal)</span>
                </div>
            </div>
        @endforeach

        <div class="struk__rule"></div>

        <div class="struk__row struk__total"><span>TOTAL</span><span>@rp($transaksi->total)</span></div>

        <div class="struk__rule"></div>

        <div class="struk__rows">
            <div class="struk__row"><span>Metode</span><span>{{ $transaksi->metode_label }}</span></div>
            @if ($transaksi->metode_bayar === 'tunai')
                <div class="struk__row"><span>Bayar</span><span>@rp($transaksi->jumlah_bayar)</span></div>
                <div class="struk__row"><span>Kembali</span><span>@rp($transaksi->kembalian)</span></div>
            @else
                @if ($transaksi->referensi)
                    <div class="struk__row"><span>Ref.</span><span>{{ $transaksi->referensi }}</span></div>
                @endif
                <div class="struk__row"><span>Status</span><span>LUNAS</span></div>
            @endif
        </div>

        <div class="struk__rule"></div>

        <div class="struk__foot">
            <p>Terima kasih. Sampai ketemu di kursi.</p>
            @if ($profil?->jam_operasional)
                <p>{{ $profil->jam_operasional }} · Walk-in</p>
            @endif
            <p>Simpan struk ini sebagai bukti transaksi.</p>
        </div>
    </div>

    <script>
        window.addEventListener('load', function() {
            window.print();
        });
    </script>
</body>

</html>
