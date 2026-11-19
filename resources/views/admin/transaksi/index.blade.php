@extends('layouts.admin')

@section('title', 'Transaksi')
@section('crumb', 'TRANSAKSI')

@section('content')
    @php
        $line = $edit?->detail->first();
        $selected = old('layanan_id', $line?->layanan_id);
        $tanggal = old('tanggal', $edit?->tanggal?->toDateString() ?? now()->toDateString());
        $hargaVal = old('harga', $line?->harga);
        $qtyVal = old('qty', $line?->qty ?? 1);
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
        @if ($edit)
            @method('PUT')
        @endif

        <div class="form-heading">
            <h2>{{ $edit ? 'Ubah transaksi ' . $edit->kode_tampil : 'Catat transaksi' }}</h2>
        </div>

        <div class="form-row">
            <div class="field @error('tanggal') has-error @enderror">
                <label class="field__label" for="tanggal">Tanggal *</label>
                <input class="field__control" type="date" id="tanggal" name="tanggal" value="{{ $tanggal }}">
                @error('tanggal')
                    <span class="field__help">!&nbsp;&nbsp;{{ $message }}</span>
                @enderror
            </div>

            <div class="field @error('layanan_id') has-error @enderror">
                <span class="field__label" id="layanan-label">Layanan *</span>
                <div class="dropdown" data-dropdown>
                    <input type="hidden" name="layanan_id" value="{{ $selected }}">
                    <button type="button" class="field__control dropdown__trigger" aria-haspopup="listbox"
                        aria-expanded="false" aria-labelledby="layanan-label">
                        <span data-dropdown-label>Pilih layanan</span>
                        <x-icon name="chevron-down" :size="16" />
                    </button>
                    <ul class="dropdown__list" role="listbox" hidden>
                        @foreach ($layanan as $l)
                            <li class="dropdown__option" role="option" aria-selected="false"
                                data-value="{{ $l->id }}" data-harga="{{ $l->harga }}">{{ $l->nama }} -
                                @rp($l->harga)</li>
                        @endforeach
                    </ul>
                </div>
                @error('layanan_id')
                    <span class="field__help">!&nbsp;&nbsp;{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="field @error('harga') has-error @enderror">
                <label class="field__label" for="harga">Harga satuan (Rp) *</label>
                <input class="field__control" type="text" inputmode="numeric" id="harga" name="harga"
                    autocomplete="off" @readonly(!auth()->user()->isAdmin())
                    value="{{ $hargaVal !== null && $hargaVal !== '' ? number_format((int) $hargaVal, 0, ',', '.') : '' }}">
                <span class="field__help">
                    @error('harga')
                        !&nbsp;&nbsp;{{ $message }}
                    @else
                        Terisi dari layanan yang dipilih.
                    @enderror
                </span>
            </div>

            <div class="field @error('qty') has-error @enderror">
                <label class="field__label" for="qty">Jumlah *</label>
                <input class="field__control" type="text" inputmode="numeric" id="qty" name="qty"
                    autocomplete="off" value="{{ $qtyVal }}">
                <span class="field__help">
                    @error('qty')
                        !&nbsp;&nbsp;{{ $message }}
                    @else
                        Minimal 1 unit layanan.
                    @enderror
                </span>
            </div>
        </div>

        <div class="divider"></div>

        <div class="total-row">
            <div class="total-row__label">
                <strong>Total transaksi</strong>
                <span data-total-calc>Rp0 × 1</span>
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
                @if ($riwayat->onFirstPage())
                    <span class="is-disabled">← Sebelumnya</span>
                @else
                    <a href="{{ $riwayat->previousPageUrl() }}">← Sebelumnya</a>
                @endif

                <span>Halaman {{ $riwayat->currentPage() }} dari {{ $riwayat->lastPage() }}</span>

                @if ($riwayat->hasMorePages())
                    <a href="{{ $riwayat->nextPageUrl() }}">Berikutnya →</a>
                @else
                    <span class="is-disabled">Berikutnya →</span>
                @endif
            </div>
        @endif
    </section>

    <div class="divider"></div>
    <p class="muted-11">Babarberan Haircuts · Ruang admin</p>
@endsection
