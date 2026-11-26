@php
    $nomor = is_numeric($i);
    $err   = fn ($f) => $nomor ? $errors->first("items.$i.$f") : '';
@endphp

<div class="item-row" data-item>
    <div class="item-row__head">
        <span class="item-row__no" data-item-no>Layanan {{ $nomor ? $i + 1 : '' }}</span>
        <button type="button" class="item-row__remove" data-item-remove>Hapus baris</button>
    </div>

    <div class="form-row">
        <div class="field @if ($err('layanan_id')) has-error @endif">
            <span class="field__label">Layanan *</span>
            <div class="dropdown" data-dropdown>
                <input type="hidden" data-f="layanan_id" name="items[{{ $i }}][layanan_id]" value="{{ $val['layanan_id'] ?? '' }}">
                <button type="button" class="field__control dropdown__trigger" aria-haspopup="listbox" aria-expanded="false">
                    <span data-dropdown-label>Pilih layanan</span>
                    <x-icon name="chevron-down" :size="16" />
                </button>
                <ul class="dropdown__list" role="listbox" hidden>
                    @foreach ($layanan as $l)
                        <li class="dropdown__option" role="option" aria-selected="false"
                            data-value="{{ $l->id }}" data-harga="{{ $l->harga }}">{{ $l->nama }} — @rp($l->harga)</li>
                    @endforeach
                </ul>
            </div>
            @if ($err('layanan_id')) <span class="field__help">!&nbsp;&nbsp;{{ $err('layanan_id') }}</span> @endif
        </div>

        <div class="field @if ($err('qty')) has-error @endif">
            <label class="field__label">Jumlah *</label>
            <input class="field__control" type="text" inputmode="numeric" autocomplete="off"
                   data-f="qty" name="items[{{ $i }}][qty]" value="{{ $val['qty'] ?? 1 }}">
            @if ($err('qty')) <span class="field__help">!&nbsp;&nbsp;{{ $err('qty') }}</span> @endif
        </div>
    </div>

    <div class="form-row">
        <div class="field @if ($err('harga')) has-error @endif">
            <label class="field__label">Harga satuan (Rp) *</label>
            <input class="field__control" type="text" inputmode="numeric" autocomplete="off"
                   data-f="harga" name="items[{{ $i }}][harga]" @readonly(! $admin)
                   value="{{ isset($val['harga']) && $val['harga'] !== '' ? number_format((int) $val['harga'], 0, ',', '.') : '' }}">
            @if ($err('harga')) <span class="field__help">!&nbsp;&nbsp;{{ $err('harga') }}</span> @endif
        </div>

        <div class="field">
            <span class="field__label">Subtotal</span>
            <div class="change-box" data-subtotal>Rp0</div>
        </div>
    </div>
</div>