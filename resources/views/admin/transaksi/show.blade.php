@extends('layouts.admin')

@section('title', 'Transaksi tersimpan')
@section('crumb', 'TRANSAKSI')

@section('content')
    @php $d = $transaksi->detail->first(); @endphp

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

        <p class="saved__meta">{{ $transaksi->kode_tampil }} · {{ $transaksi->tanggal->translatedFormat('d F Y') }}</p>
        <div class="divider"></div>

        <div class="saved__detail">
            <div class="saved__service">
                <strong>{{ $d?->nama_layanan }}</strong>
                <span>{{ $d?->qty }} × @rp($d?->harga ?? 0)</span>
            </div>
            <p class="saved__total">@rp($transaksi->total)</p>
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
