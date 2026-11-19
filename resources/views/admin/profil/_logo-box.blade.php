@php $err = $errors->first($field); @endphp

<div class="logo-upload @if ($err) has-error @endif">
    <span class="field__label">{{ $label }}</span>
    <div class="logo-box">
        <div class="logo-box__img {{ $dark ? 'logo-box__img--dark' : '' }}">
            @if ($url)
                <img src="{{ $url }}" alt="{{ $label }}">
            @else
                <span>Logo asli</span>
            @endif
        </div>
        <div class="logo-box__text">
            <span>{{ $url ? 'Logo terpasang. Pilih berkas baru untuk menggantinya.' : $kosong }}</span>
            <input type="file" name="{{ $field }}" accept=".png,.jpg,.jpeg,.webp,.svg">
            @if ($err) <span>!&nbsp;&nbsp;{{ $err }}</span> @endif
        </div>
    </div>
</div>