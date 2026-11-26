@extends('layouts.admin')

@section('title', 'Transaksi tersimpan')
@section('crumb', 'TRANSAKSI')

@section('content')

<div class="page-heading">
    <div class="page-heading__text">
        <h1 class="page-heading__title">Transaksi.</h1>
    </div>
</div>

<section class="saved">
    <div class="saved__head">
        <x-icon name="circle-check" :size="28" />
        <p class="saved__title">Transaksi berhasil disimpan.</p>
    </div>

    <p class="saved__meta">
        {{ $transaksi->kode_tampil }} · {{ $transaksi->tanggal->translatedFormat('d F Y') }}
        @if ($transaksi->user) · Kasir: {{ $transaksi->user->name }} @endif
    </p>
    <div class="divider"></div>

    <div class="saved__items">
        @foreach ($transaksi->detail as $d)
            <div class="saved__item">
                <div class="saved__service">
                    <strong>{{ $d->nama_layanan }}</strong>
                    <span>{{ $d->qty }} × @rp($d->harga)</span>
                </div>
                <p class="saved__subtotal">@rp($d->subtotal)</p>
            </div>
        @endforeach
    </div>

    <div class="divider"></div>

    <div class="saved__detail">
        <span class="saved__label">TOTAL</span>
        <p class="saved__total">@rp($transaksi->total)</p>
    </div>

    <div class="saved__pay">
        <div>
            <span class="k">METODE</span>
            <span class="v">{{ $transaksi->metode_label }}@if ($transaksi->referensi) · {{ $transaksi->referensi }}@endif</span>
        </div>
        @if ($transaksi->metode_bayar === 'tunai')
            <div><span class="k">UANG DITERIMA</span><span class="v">@rp($transaksi->jumlah_bayar)</span></div>
            <div><span class="k">KEMBALIAN</span><span class="v v--strong">@rp($transaksi->kembalian)</span></div>
        @else
            <div><span class="k">STATUS</span><span class="v">Lunas</span></div>
        @endif
    </div>

    <div class="form-actions">
        <x-button :href="route('admin.transaksi.struk', $transaksi)" icon="printer" target="_blank" rel="noopener">Cetak struk</x-button>
        <x-button variant="secondary" :href="route('admin.transaksi.index') . '#catat'">Catat transaksi baru</x-button>
        <x-button variant="secondary" :href="route('admin.transaksi.index')">Lihat riwayat</x-button>
    </div>
</section>

<h2 class="section-title">Catatan yang disimpan</h2>
@include('admin.transaksi._table', ['rows' => collect([$transaksi])])
<p class="muted-12">Termasuk dalam total keseluruhan @rp($totalSemua) · tidak dihitung dua kali.</p>
@endsection