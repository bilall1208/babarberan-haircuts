@extends('layouts.admin')

@section('title', 'Laporan')
@section('crumb', 'LAPORAN')

@section('content')

    <div class="page-heading__actions">
        <x-button variant="secondary" :href="route('admin.laporan.excel', request()->query())" icon="download">Unduh Excel</x-button>
        <x-button variant="secondary" :href="route('admin.laporan.pdf', request()->query())" icon="download">Unduh PDF</x-button>
        <x-button :href="route('admin.laporan.cetak', request()->query())" icon="printer" target="_blank" rel="noopener">Cetak laporan</x-button>
    </div>

    <form method="GET" action="{{ route('admin.laporan.index') }}" class="report-filters">
        <div class="form-row">
            <div class="field">
                <label class="field__label" for="dari">Dari tanggal</label>
                <input class="field__control" type="date" id="dari" name="dari"
                    value="{{ $dari->toDateString() }}">
            </div>
            <div class="field">
                <label class="field__label" for="sampai">Sampai tanggal</label>
                <input class="field__control" type="date" id="sampai" name="sampai"
                    value="{{ $sampai->toDateString() }}">
            </div>
            <div class="field">
                <label class="field__label" for="layanan">Layanan</label>
                <div class="select">
                    <select class="field__control" id="layanan" name="layanan">
                        <option value="">Semua layanan</option>
                        @foreach ($daftarLayanan as $nama)
                            <option value="{{ $nama }}" @selected($layananDipilih === $nama)>{{ $nama }}</option>
                        @endforeach
                    </select>
                    <x-icon name="chevron-down" :size="16" />
                </div>
            </div>
        </div>
        <div class="form-actions">
            <x-button type="submit">Terapkan filter</x-button>
            <x-button variant="secondary" :href="route('admin.laporan.index')">Reset</x-button>
        </div>
    </form>

    <div class="metrics">
        <div class="metric">
            <p class="metric__label">TOTAL PENDAPATAN</p>
            <p class="metric__value">@rp($totalPendapatan)</p>
            <p class="metric__sub">{{ $layananDipilih ?? 'Semua layanan' }} · {{ $periodeShort }}</p>
        </div>
        <div class="metric">
            <p class="metric__label">TOTAL TRANSAKSI</p>
            <p class="metric__value">{{ $totalTransaksi }}</p>
            <p class="metric__sub">Jumlah catatan transaksi</p>
        </div>
        <div class="metric">
            <p class="metric__label">UNIT LAYANAN</p>
            <p class="metric__value">{{ $totalUnit }}</p>
            <p class="metric__sub">Akumulasi kolom jumlah</p>
        </div>
    </div>

    <div class="method-summary">
        @foreach ($perMetode as $m)
            <div class="method-summary__item">
                <span class="k">{{ strtoupper($m['label']) }} · {{ $m['jml'] }} transaksi</span>
                <strong>@rp($m['total'])</strong>
            </div>
        @endforeach
    </div>

    @if ($baris->isEmpty())

        <div class="empty-state">
            <x-icon name="file-search" :size="36" stroke="1.25" />
            <p class="empty-state__title">Tidak ada transaksi.</p>
            <p class="empty-state__text">Tidak ada transaksi pada {{ $periodeLong }}. Ubah periode atau layanan untuk
                melihat catatan lain.</p>
            <x-button :href="route('admin.laporan.index')">Reset filter</x-button>
        </div>
    @else
        <section class="report-table">
            <div class="report-head">
                <h2>Rincian periode</h2>
            </div>

            <div class="table hide-mobile">
                <div class="table__head">
                    <span class="t-id">ID</span><span class="t-date">TANGGAL</span><span class="t-name">LAYANAN</span>
                    <span class="t-price">HARGA</span><span class="t-qty">JML</span><span class="t-total">TOTAL</span>
                    <span class="t-method">METODE</span>
                </div>

                @foreach ($baris as $b)
                    <div class="table__row">
                        <span class="t-id t-muted">TRX-{{ str_pad($b->transaksi_id, 3, '0', STR_PAD_LEFT) }}</span>
                        <span class="t-date">{{ \Carbon\Carbon::parse($b->tgl)->translatedFormat('d M Y') }}</span>
                        <span class="t-name">{{ $b->nama_layanan }}</span>
                        <span class="t-price">@rp($b->harga)</span>
                        <span class="t-qty">{{ $b->qty }}</span>
                        <span class="t-method">{{ $labelMetode[$b->metode] ?? $b->metode }}</span>
                        <span class="t-total">@rp($b->subtotal)</span>
                    </div>
                @endforeach
            </div>

            <div class="record-list show-mobile">
                @foreach ($baris as $b)
                    <div class="record">
                        <p class="record__meta">TRX-{{ str_pad($b->transaksi_id, 3, '0', STR_PAD_LEFT) }} / {{ \Carbon\Carbon::parse($b->tgl)->translatedFormat('d M Y') }} / {{ $labelMetode[$b->metode] ?? $b->metode }}</p>
                        <div class="record__amount">
                            <span class="record__name">{{ $b->nama_layanan }}</span>
                            <span class="record__total">@rp($b->subtotal)</span>
                        </div>
                        <p class="record__calc">{{ $b->qty }} × @rp($b->harga)</p>
                    </div>
                @endforeach
            </div>

            <div class="report-total">
                <span>Total · {{ $totalTransaksi }} transaksi</span>
                <strong>@rp($totalPendapatan)</strong>
            </div>

            @foreach ($catatan as $teks)
                <p class="muted-12">{{ $teks }}</p>
            @endforeach
            <p class="muted-12">Laporan cetak menggunakan filter dan angka yang sama.</p>
        </section>

    @endif

    <div class="divider"></div>
    <p class="muted-11">Babarberan Haircuts · Ruang admin</p>
@endsection
