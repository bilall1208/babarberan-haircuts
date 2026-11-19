@extends('layouts.admin')

@section('title', 'Dashboard')
@section('crumb', 'DASHBOARD')

@php $rp = fn ($n) => 'Rp' . number_format($n, 0, ',', '.'); @endphp

@section('content')

<div class="page-heading">
    <div class="page-heading__text">
        <h1 class="page-heading__title">Ringkasan usaha.</h1>
        <p class="page-heading__sub">{{ now()->translatedFormat('l, d F Y') }} · Gambaran catatan transaksi.</p>
    </div>
    <x-button :href="route('admin.transaksi.index') . '#catat'" icon="plus">Catat transaksi</x-button>
</div>

<div class="metrics">
    <div class="metric">    
        <p class="metric__label">PENDAPATAN PERIODE</p>
        <p class="metric__value">{{ $rp($pendapatanPeriode) }}</p>
        <p class="metric__sub">{{ $awal->translatedFormat('d M') }}–{{ $akhir->translatedFormat('d M Y') }} · {{ $jumlahTransaksi }} transaksi</p>
    </div>
    <div class="metric">
        <p class="metric__label">HARI INI</p>
        <p class="metric__value">{{ $rp($pendapatanHariIni) }}</p>
        <p class="metric__sub">{{ now()->translatedFormat('d M Y') }} · {{ $transaksiHariIni }} transaksi</p>
    </div>
    <div class="metric">
        <p class="metric__label">JUMLAH LAYANAN</p>
        <p class="metric__value">{{ $unitLayanan }}</p>
        <p class="metric__sub">Unit layanan dalam {{ $jumlahTransaksi }} transaksi</p>
    </div>
</div>

<div class="dash-detail">
    <section class="card-white">
        <h2 class="card-title">Pendapatan per hari</h2>

        <div class="bars">
            @forelse ($harian as $h)
                <div class="bar">
                    <div class="bar__labels">
                        <span>{{ $h->tanggal->translatedFormat('d M') }}</span>
                        <strong>{{ $rp($h->total) }}</strong>
                    </div>
                    <div class="bar__track">
                        <div class="bar__fill" style="width: {{ round($h->total / $maks * 100) }}%"></div>
                    </div>
                </div>
            @empty
                <p class="muted-12">Belum ada transaksi pada periode ini.</p>
            @endforelse
        </div>

        <p class="muted-11">Periode {{ $awal->translatedFormat('d M') }}–{{ $akhir->translatedFormat('d M Y') }}</p>
    </section>

    <aside class="card-line">
        <h2 class="card-title">Akses cepat</h2>
        <x-button variant="secondary" block :href="Route::has('admin.layanan.index') ? route('admin.layanan.index') : '#'">Kelola layanan</x-button>
        <x-button variant="secondary" block :href="Route::has('admin.laporan.index') ? route('admin.laporan.index') : '#'">Lihat laporan</x-button>
        <div class="divider"></div>
        @php $p = \App\Models\ProfilUsaha::current(); @endphp
        <p class="muted-12">Jam buka publik: {{ $p?->jam_operasional ?: '13:00–selesai' }}.{{ ($p?->senin_tutup ?? true) ? ' Senin tutup.' : '' }}</p>    
    </aside>
</div>

<section class="recent">
    <div class="recent__head">
        <h2>Transaksi terbaru</h2>
        <span>{{ $terbaru->count() }} dari {{ $totalBaris }}</span>
    </div>

    <div class="table hide-mobile">
        <div class="table__head">
            <span class="t-id">ID</span><span class="t-date">TANGGAL</span><span class="t-name">LAYANAN</span>
            <span class="t-price">HARGA</span><span class="t-qty">JML</span><span class="t-total">TOTAL</span>
        </div>

        @forelse ($terbaru as $d)
            <div class="table__row">
                <span class="t-id t-muted">TRX-{{ str_pad($d->transaksi_id, 3, '0', STR_PAD_LEFT) }}</span>
                <span class="t-date">{{ $d->transaksi->tanggal->translatedFormat('d M Y') }}</span>
                <span class="t-name">{{ $d->nama_layanan }}</span>
                <span class="t-price">{{ $rp($d->harga) }}</span>
                <span class="t-qty">{{ $d->qty }}</span>
                <span class="t-total">{{ $rp($d->subtotal) }}</span>
            </div>
        @empty
            <div class="table__row"><span class="t-muted">Belum ada transaksi.</span></div>
        @endforelse
    </div>
    <div class="record-list show-mobile">
    @forelse ($terbaru as $d)
        <div class="record">
            <p class="record__meta">TRX-{{ str_pad($d->transaksi_id, 3, '0', STR_PAD_LEFT) }} / {{ $d->transaksi->tanggal->translatedFormat('d M Y') }}</p>
            <div class="record__amount">
                <span class="record__name">{{ $d->nama_layanan }}</span>
                <span class="record__total">@rp($d->subtotal)</span>
            </div>
            <p class="record__calc">{{ $d->qty }} × @rp($d->harga)</p>
        </div>
    @empty
        <p class="muted-12">Belum ada transaksi.</p>
    @endforelse
</div>
    <a href="{{ route('admin.transaksi.index') }}" class="link-strong">Lihat semua transaksi ↗</a>
</section>

<div class="divider"></div>
<p class="muted-11">Babarberan Haircuts · Ruang admin</p>

@endsection