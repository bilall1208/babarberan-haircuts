@props(['on' => 'light', 'href' => null])

@php
    $profil = \App\Models\ProfilUsaha::current();
    $src    = $on === 'dark' ? $profil?->logo_putih_url : $profil?->logo_url;
    $tag    = $href ? 'a' : 'div';
@endphp

<{{ $tag }} @if ($href) href="{{ $href }}" @endif
    {{ $attributes->merge(['class' => 'brand-logo brand-logo--' . $on . ($src ? ' has-image' : '')]) }}>
    @if ($src)
        <img src="{{ $src }}" alt="{{ $profil->nama_usaha }}">
    @else
        <span>Logo asli</span>
    @endif
</{{ $tag }}>