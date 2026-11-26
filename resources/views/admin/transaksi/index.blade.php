@extends('layouts.admin')

@section('title', 'Transaksi')
@section('crumb', 'TRANSAKSI')

@section('content')
@php
    $admin    = auth()->user()->isAdmin();
    $tanggal  = old('tanggal', $edit?->tanggal?->toDateString() ?? now()->toDateString());
    $metode   = old('metode_bayar', $edit?->metode_bayar ?? 'tunai');
    $bayarVal = old('jumlah_bayar', ($edit && $edit->metode_bayar === 'tunai') ? $edit->jumlah_bayar : null);

    // baris layanan: dari input lama (validasi gagal), dari transaksi yang diedit, atau satu baris kosong
    if (old('items')) {
        $baris = array_values(old('items'));
    } elseif ($edit) {
        $baris = $edit->detail->map(fn ($d) => ['layanan_id' => $d->layanan_id, 'qty' => $d->qty, 'harga' => $d->harga])->all();
    } else {
        $baris = [['layanan_id' => '', 'qty' => 1, 'harga' => '']];
    }
@endphp

<div class="page-heading">
    <div class="page-heading__text">
        <h1 class="page-heading__title">Transaksi.</h1>
        <p class="page-heading__sub">Catat layanan yang selesai, periksa harga, dan kelola riwayat harian.</p>
    </div>
</div>

<form id="catat" class="card-white card-form" method="POST" novalidate data-transaksi-form
      action="{{ $edit ? route('admin.transaksi.update', $edit) : route('admin.transaksi.store') }}">
    @csrf
    @if ($edit) @method('PUT') @endif

    <div class="form-heading">
        <h2>{{ $edit ? 'Ubah transaksi ' . $edit->kode_tampil : 'Catat transaksi' }}</h2>
    </div>

    <div class="form-row">
        <div class="field @error('tanggal') has-error @enderror">
            <label class="field__label" for="tanggal">Tanggal *</label>
            <input class="field__control" type="date" id="tanggal" name="tanggal" value="{{ $tanggal }}">
            @error('tanggal') <span class="field__help">!&nbsp;&nbsp;{{ $message }}</span> @enderror
        </div>
    </div>

    {{-- Daftar layanan dalam satu struk --}}
    <div class="items" data-items>
        @foreach ($baris as $i => $val)
            @include('admin.transaksi._item', ['i' => $i, 'val' => $val, 'layanan' => $layanan, 'admin' => $admin])
        @endforeach
    </div>
    @error('items') <span class="field__help">!&nbsp;&nbsp;{{ $message }}</span> @enderror

    <div>
        <x-button variant="secondary" icon="plus" data-item-add>Tambah layanan</x-button>
    </div>

    <template id="item-template">
        @include('admin.transaksi._item', ['i' => '__i__', 'val' => [], 'layanan' => $layanan, 'admin' => $admin])
    </template>

    <div class="divider"></div>

    {{-- Pembayaran --}}
    <div class="field @error('metode_bayar') has-error @enderror">
        <span class="field__label">Metode pembayaran *</span>
        <div class="pay-options" role="radiogroup" aria-label="Metode pembayaran">
            @foreach (['tunai' => 'Tunai', 'qris' => 'QRIS', 'transfer' => 'Transfer'] as $nilai => $label)
                <label class="pay-option">
                    <input type="radio" name="metode_bayar" value="{{ $nilai }}" data-pay-method @checked($metode === $nilai)>
                    <span>{{ $label }}</span>
                </label>
            @endforeach
        </div>
        @error('metode_bayar') <span class="field__help">!&nbsp;&nbsp;{{ $message }}</span> @enderror
    </div>

    <div class="form-row" data-pay-cash>
        <div class="field @error('jumlah_bayar') has-error @enderror">
            <label class="field__label" for="jumlah_bayar">Uang diterima (Rp) *</label>
            <input class="field__control" type="text" inputmode="numeric" id="jumlah_bayar" name="jumlah_bayar" autocomplete="off"
                   value="{{ $bayarVal ? number_format((int) $bayarVal, 0, ',', '.') : '' }}">
            <span class="field__help">
                @error('jumlah_bayar') !&nbsp;&nbsp;{{ $message }} @else Isi nominal uang dari pelanggan. @enderror
            </span>
        </div>
        <div class="field">
            <span class="field__label">Kembalian</span>
            <div class="change-box" data-change>Rp0</div>
            <span class="field__help" data-change-help>&nbsp;</span>
        </div>
    </div>

    <div class="quick-cash" data-pay-cash>
        <button type="button" class="chip" data-cash="pas">Uang pas</button>
        <button type="button" class="chip" data-cash="20000">Rp20.000</button>
        <button type="button" class="chip" data-cash="50000">Rp50.000</button>
        <button type="button" class="chip" data-cash="100000">Rp100.000</button>
    </div>

    <div class="field" data-pay-ref hidden>
        <label class="field__label" for="referensi">No. referensi (opsional)</label>
        <input class="field__control" id="referensi" name="referensi" type="text" maxlength="50" autocomplete="off"
               placeholder="Mis. nomor referensi QRIS atau bukti transfer"
               value="{{ old('referensi', $edit?->referensi) }}">
        <span class="field__help">Pembayaran non-tunai dicatat lunas sesuai total.</span>
    </div>

    <div class="divider"></div>

    <div class="total-row">
        <div class="total-row__label">
            <strong>Total transaksi</strong>
            <span data-total-calc>1 layanan · 0 unit</span>
        </div>
        <p class="total-row__value" data-total-value>Rp0</p>
    </div>

    <div class="form-actions">
        <x-button type="submit">{{ $edit ? 'Simpan perubahan' : 'Simpan transaksi' }}</x-button>
        @if ($edit)
            <x-button variant="secondary" :href="route('admin.transaksi.index')">Batal</x-button>
        @else
            <x-button variant="secondary" data-form-reset>Reset formulir</x-button>
        @endif
    </div>
</form>

<section class="history">
    <div class="history__head">
        <h2>Riwayat transaksi</h2>
        @if ($jumlahTransaksi)
            <span>
                {{ \Carbon\Carbon::parse($awal)->translatedFormat('d M') }}–{{ \Carbon\Carbon::parse($akhir)->translatedFormat('d M Y') }}
                · {{ $jumlahTransaksi }} transaksi
            </span>
        @endif
    </div>

    @include('admin.transaksi._table', ['rows' => $riwayat])

    <div class="history__total">
        <span>{{ $jumlahTransaksi }} transaksi · {{ $totalUnit }} unit</span>
        <strong>@rp($totalSemua)</strong>
    </div>

    @if ($riwayat->hasPages())
        <div class="pager">
            @if ($riwayat->onFirstPage()) <span class="is-disabled">← Sebelumnya</span>
            @else <a href="{{ $riwayat->previousPageUrl() }}">← Sebelumnya</a> @endif

            <span>Halaman {{ $riwayat->currentPage() }} dari {{ $riwayat->lastPage() }}</span>

            @if ($riwayat->hasMorePages()) <a href="{{ $riwayat->nextPageUrl() }}">Berikutnya →</a>
            @else <span class="is-disabled">Berikutnya →</span> @endif
        </div>
    @endif
</section>

<div class="divider"></div>
<p class="muted-11">Babarberan Haircuts · Ruang admin</p>
@endsection