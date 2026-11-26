@php
    $links = [
        ['label' => 'Home',    'href' => url('/')],
        ['label' => 'Tentang', 'href' => url('/') . '#tentang'],
        ['label' => 'Layanan', 'href' => url('/') . '#layanan'],
        ['label' => 'Galeri',  'href' => url('/') . '#galeri'],
        ['label' => 'Kontak',  'href' => url('/') . '#kontak'],
        ['label' => 'Admin',   'href' => url('/admin/login')],
    ];
@endphp

<header class="container">
    <nav class="pubnav" aria-label="Navigasi utama">
        <x-logo :href="url('/')" />

        <div class="pubnav__links">
            @foreach ($links as $i => $link)
                <a href="{{ $link['href'] }}" data-label="{{ $link['label'] }}" @class(['is-active' => $i === 0])>{{ $link['label'] }}</a>
            @endforeach
        </div>

        <button type="button" class="pubnav__toggle" data-menu-open aria-label="Buka menu" aria-controls="mobile-menu">
            <x-icon name="menu" :size="20" />
        </button>
    </nav>
    <div class="divider"></div>
</header>

{{-- Mobile menu terbuka (Figma: "Babarberan · Mobile menu open") --}}
<div class="mobile-menu" id="mobile-menu" role="dialog" aria-modal="true" aria-label="Menu">
    <div class="mobile-menu__header">
        <x-logo :href="url('/')" />
        <button type="button" data-menu-close aria-label="Tutup menu">
            <x-icon name="x" :size="20" />
        </button>
    </div>

    <p class="mobile-menu__eyebrow">JELAJAHI BABARBERAN</p>

    <div class="mobile-menu__nav">
        @foreach ($links as $link)
            <a href="{{ $link['href'] }}" class="mobile-menu__link" data-menu-link>
                <span class="mobile-menu__label">
                    <span>{{ $link['label'] }}</span>
                    <x-icon name="arrow-up-right" :size="24" />
                </span>
                <span class="divider"></span>
            </a>
        @endforeach
    </div>

    <div class="mobile-menu__visit">
        <span class="mobile-menu__visit-title">WALK-IN ONLY</span>
        <span class="mobile-menu__visit-hours">Selasa–Minggu · 13:00–selesai</span>
        <span class="mobile-menu__visit-closed">Senin tutup</span>
    </div>
</div>
