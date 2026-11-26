@php
    $admin = auth()->user()->isAdmin();

    $hapusAttr = fn ($t) => [
        'data-modal-confirm' => '',
        'data-title'  => 'Konfirmasi hapus transaksi',
        'data-desc'   => 'Transaksi akan dihapus dari riwayat dan ringkasan. Pastikan pilihanmu sebelum melanjutkan.',
        'data-name'   => $t->detail->count() > 1 ? $t->detail->count() . ' layanan' : $t->detail->first()?->nama_layanan,
        'data-detail' => 'Rp' . number_format($t->total, 0, ',', '.'),
        'data-note'   => $t->kode_tampil . ' · ' . $t->tanggal->translatedFormat('d M Y') . '. Total di dashboard dan laporan ikut berkurang.',
        'data-ok'     => 'Ya, hapus transaksi',
    ];
@endphp

{{-- Tabel (layar lebar) --}}
<div class="table hide-mobile">
    <div class="table__head">
        <span class="t-id">ID</span><span class="t-date">TANGGAL</span><span class="t-name">LAYANAN</span>
        <span class="t-method">METODE</span><span class="t-total">TOTAL</span>
        <span class="t-actions">AKSI</span>
    </div>

    @forelse ($rows as $t)
        <div class="table__row">
            <span class="t-id t-muted">{{ $t->kode_tampil }}</span>
            <span class="t-date">{{ $t->tanggal->translatedFormat('d M Y') }}</span>
            <span class="t-name t-lines">
                @foreach ($t->detail as $d)
                    <span class="t-line"><b>{{ $d->nama_layanan }}</b><em>{{ $d->qty }} × @rp($d->harga)</em></span>
                @endforeach
            </span>
            <span class="t-method">{{ $t->metode_label }}</span>
            <span class="t-total">@rp($t->total)</span>
            <span class="t-actions">
                <a href="{{ route('admin.transaksi.struk', $t) }}" target="_blank" rel="noopener" aria-label="Cetak struk {{ $t->kode_tampil }}">
                    <x-icon name="printer" :size="15" />
                </a>
                @if ($admin)
                    <a href="{{ route('admin.transaksi.edit', $t) }}" aria-label="Ubah {{ $t->kode_tampil }}">
                        <x-icon name="pencil" :size="15" />
                    </a>
                    <form method="POST" action="{{ route('admin.transaksi.destroy', $t) }}"
                          @foreach ($hapusAttr($t) as $k => $v) {{ $k }}="{{ $v }}" @endforeach>
                        @csrf @method('DELETE')
                        <button type="submit" aria-label="Hapus {{ $t->kode_tampil }}"><x-icon name="trash-2" :size="15" /></button>
                    </form>
                @endif
            </span>
        </div>
    @empty
        <div class="table__row"><span class="t-muted">Belum ada transaksi.</span></div>
    @endforelse
</div>

{{-- Kartu (mobile) --}}
<div class="record-list show-mobile">
    @forelse ($rows as $t)
        <div class="record">
            <p class="record__meta">{{ $t->kode_tampil }} / {{ $t->tanggal->translatedFormat('d M Y') }} / {{ $t->metode_label }}</p>
            <div class="record__amount">
                <span class="record__name">{{ $t->detail->count() > 1 ? $t->detail->count() . ' layanan' : $t->detail->first()?->nama_layanan }}</span>
                <span class="record__total">@rp($t->total)</span>
            </div>
            @foreach ($t->detail as $d)
                <p class="record__calc"><span>{{ $d->nama_layanan }} · {{ $d->qty }} × @rp($d->harga)</span></p>
            @endforeach
            <div class="record__calc">
                <a href="{{ route('admin.transaksi.struk', $t) }}" target="_blank" rel="noopener" class="record__action">Struk</a>
                @if ($admin)
                    <a href="{{ route('admin.transaksi.edit', $t) }}" class="record__action">Edit</a>
                    <form method="POST" action="{{ route('admin.transaksi.destroy', $t) }}"
                          @foreach ($hapusAttr($t) as $k => $v) {{ $k }}="{{ $v }}" @endforeach>
                        @csrf @method('DELETE')
                        <button type="submit" class="record__action">Hapus</button>
                    </form>
                @endif
            </div>
        </div>
    @empty
        <p class="muted-12">Belum ada transaksi.</p>
    @endforelse
</div>