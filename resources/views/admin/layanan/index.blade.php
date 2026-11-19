@extends('layouts.admin')

@section('title', 'Kelola Layanan')
@section('crumb', 'KELOLA LAYANAN')

@section('content')

    <div class="page-heading">
        <div class="page-heading__text">
            <h1 class="page-heading__title">Kelola layanan.</h1>
            <p class="page-heading__sub">Daftar harga yang ditampilkan di website dan formulir transaksi.</p>
        </div>
        <x-button :href="route('admin.layanan.create')" icon="plus">Tambah layanan</x-button>
    </div>

    @if ($daftar->isEmpty())

        <p class="muted-12">0 layanan</p>

        <div class="empty-state">
            <x-icon name="list-plus" :size="36" stroke="1.25" />
            <p class="empty-state__title">Belum ada layanan.</p>
            <p class="empty-state__text">Tambahkan layanan pertama untuk mengisi daftar harga dan pilihan pada formulir
                transaksi.</p>
            <x-button :href="route('admin.layanan.create')">Tambah layanan</x-button>
        </div>
    @else
        <div class="toolbar">
            <div class="field">
                <label class="field__label" for="cari">Pencarian</label>
                <input class="field__control" id="cari" type="text" placeholder="Cari nama layanan"
                    autocomplete="off" data-service-search>
            </div>
            <p class="muted-12" data-service-count>{{ $daftar->count() }} entri / {{ $jenis }} jenis layanan</p>
        </div>

        <div class="svc-list">
            <div class="svc-head">
                <span class="svc-no">NO.</span><span class="svc-name">NAMA LAYANAN</span>
                <span class="svc-price">HARGA</span><span class="svc-status">STATUS</span>
                <span class="svc-actions">AKSI</span>
            </div>

            @foreach ($daftar as $l)
                <div class="svc-row" data-service-row data-name="{{ \Illuminate\Support\Str::lower($l->nama) }}">
                    <span class="svc-no">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <span class="svc-name">
                        {{ $l->nama }}
                        @if ($l->deskripsi)
                            <small class="svc-desc">{{ $l->deskripsi }}</small>
                        @endif
                    </span>
                    <span class="svc-price">@rp($l->harga)</span>
                    <span class="svc-status">Aktif</span>
                    <span class="svc-actions">
                        <a href="{{ route('admin.layanan.edit', $l) }}">Edit</a>
                        <form method="POST" action="{{ route('admin.layanan.destroy', $l) }}" data-modal-confirm
                            data-title="Konfirmasi hapus layanan"
                            data-desc="Layanan akan dihapus dari daftar aktif. Pastikan pilihanmu sebelum melanjutkan."
                            data-name="{{ $l->nama }}" data-detail="@rp($l->harga)"
                            data-note="Catatan transaksi yang sudah tersimpan tetap ada di riwayat."
                            data-ok="Ya, hapus layanan">
                            @csrf @method('DELETE')
                            <button type="submit">Hapus</button>
                        </form>
                    </span>
                </div>
            @endforeach
        </div>

        @foreach ($catatan as $teks)
            <div class="note-card">
                <x-icon name="info" :size="20" />
                <p>{{ $teks }}</p>
            </div>
        @endforeach

        <p class="muted-12">Menampilkan seluruh {{ $daftar->count() }} entri</p>

    @endif

    <div class="divider"></div>
    <p class="muted-11">Babarberan Haircuts · Ruang admin</p>

    {{-- Modal tambah / edit --}}
    @if ($mode)
        <div class="modal">
            <a href="{{ route('admin.layanan.index') }}" class="modal__scrim" aria-label="Tutup"></a>

            <form class="modal__dialog" method="POST" novalidate role="dialog" aria-modal="true"
                aria-labelledby="svc-title"
                action="{{ $mode === 'ubah' ? route('admin.layanan.update', $edit) : route('admin.layanan.store') }}">
                @csrf
                @if ($mode === 'ubah')
                    @method('PUT')
                @endif

                <div class="modal__head">
                    <h2 id="svc-title">{{ $mode === 'ubah' ? 'Edit layanan' : 'Tambah layanan' }}</h2>
                    <a href="{{ route('admin.layanan.index') }}" data-modal-esc aria-label="Tutup"><x-icon name="x"
                            :size="20" /></a>
                </div>

                @if ($mode === 'tambah')
                    <p class="modal__desc">Masukkan nama layanan dan harga.</p>
                @endif

                <div class="field @error('nama') has-error @enderror">
                    <label class="field__label" for="nama">Nama layanan *</label>
                    <input class="field__control" id="nama" name="nama" type="text" autocomplete="off" autofocus
                        value="{{ old('nama', $edit?->nama) }}">
                    @error('nama')
                        <span class="field__help">!&nbsp;&nbsp;{{ $message }}</span>
                    @enderror
                </div>

                <div class="field @error('harga') has-error @enderror">
                    <label class="field__label" for="harga">Harga (Rp) *</label>
                    <input class="field__control" id="harga" name="harga" type="text" inputmode="numeric"
                        autocomplete="off" value="{{ old('harga', $edit?->harga) }}">
                    <span class="field__help">
                        @error('harga')
                            !&nbsp;&nbsp;{{ $message }}
                        @else
                            {{ $hargaLain }}
                        @enderror
                    </span>
                </div>

                <div class="field @error('deskripsi') has-error @enderror">
                    <label class="field__label" for="deskripsi">Deskripsi</label>
                    <textarea class="field__control" id="deskripsi" name="deskripsi" rows="3"
                        placeholder="Opsional, mis. Potongan untuk keseharianmu.">{{ old('deskripsi', $edit?->deskripsi) }}</textarea>
                    <span class="field__help">
                        @error('deskripsi')
                            !&nbsp;&nbsp;{{ $message }}
                        @else
                            Tampil di bawah nama layanan pada halaman publik.
                        @enderror
                    </span>
                </div>

                <div class="form-actions">
                    <x-button type="submit">{{ $mode === 'ubah' ? 'Simpan perubahan' : 'Tambah layanan' }}</x-button>
                    <x-button variant="secondary" :href="route('admin.layanan.index')">Batal</x-button>
                </div>
            </form>
        </div>
    @endif
@endsection
