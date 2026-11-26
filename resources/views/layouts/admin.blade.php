<!DOCTYPE html>
<html lang="id">

<head>
    @include('partials.favicon')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') - Babarberan Haircuts</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/babarberan.css') }}">
    <link rel="stylesheet" href="{{ asset('css/motion.css') }}">
</head>

<body>
    @php
        $admin = auth()->user()->isAdmin();
        $peran = $admin ? 'Admin' : 'Kasir';

        $semuaMenu = [
            ['Dashboard', 'admin.dashboard', 'admin.dashboard', true],
            ['Kelola Profil', 'admin.profil.edit', 'admin.profil.*', true],
            ['Kelola Layanan', 'admin.layanan.index', 'admin.layanan.*', true],
            ['Transaksi', 'admin.transaksi.index', 'admin.transaksi.*', false],
            ['Laporan', 'admin.laporan.index', 'admin.laporan.*', true],
        ];

        // true = khusus admin
        $menu = array_values(array_filter($semuaMenu, fn($m) => !$m[3] || $admin));
    @endphp

    <div class="admin">
        <header class="mobile-admin">
            <div class="mobile-bar">
                <x-logo :href="route('admin.dashboard')" />
                <span class="mobile-bar__user">{{ $peran }} · {{ auth()->user()->name }}</span>
            </div>

            <nav class="mobile-nav" aria-label="Navigasi">
                @php $baris = array_chunk(array_merge($menu, [['Keluar', null, null]]), 3); @endphp

                @foreach ($baris as $i => $row)
                    <div @class(['mobile-nav__row', 'mobile-nav__row--primary' => $i === 0])>
                        @foreach ($row as [$label, $routeName, $pattern])
                            @if ($routeName)
                                <a href="{{ route($routeName) }}" @class([
                                    'mobile-nav__item',
                                    'is-active' => request()->routeIs($pattern),
                                ])>{{ $label }}</a>
                            @else
                                <form method="POST" action="{{ route('admin.logout') }}" class="mobile-nav__form"
                                    data-modal-confirm data-title="Konfirmasi keluar akun"
                                    data-desc="Kamu akan keluar dari ruang kerja Babarberan Haircuts. Pastikan semua perubahan sudah tersimpan."
                                    data-name="{{ auth()->user()->name }}" data-detail="{{ $peran }}"
                                    data-note="Sesi di perangkat ini akan diakhiri." data-ok="Ya, keluar">
                                    @csrf
                                    <button type="submit" class="mobile-nav__item">Keluar</button>
                                </form>
                            @endif
                        @endforeach
                    </div>
                @endforeach
            </nav>
        </header>
        <aside class="sidebar">
            <x-logo on="dark" :href="route('admin.dashboard')" />

            <div class="sidebar__identity">
                <p class="sidebar__name">Babarberan Haircuts</p>
                <p class="sidebar__sub">RUANG {{ strtoupper($peran) }}</p>
            </div>

            <nav class="sidebar__nav" aria-label="Navigasi admin">
                @foreach ($menu as [$label, $routeName, $pattern])
                    <a href="{{ Route::has($routeName) ? route($routeName) : '#' }}"
                        @class(['sidebar__item', 'is-active' => request()->routeIs($pattern)])>{{ $label }}</a>
                @endforeach
            </nav>

            <div class="sidebar__divider"></div>

            <a href="{{ url('/') }}" class="sidebar__link">←&nbsp;&nbsp;Lihat website</a>

            <form method="POST" action="{{ route('admin.logout') }}" data-modal-confirm
                data-title="Konfirmasi keluar akun"
                data-desc="Kamu akan keluar dari ruang {{ $peran }} Babarberan Haircuts. Pastikan semua perubahan sudah tersimpan."
                data-name="{{ auth()->user()->name }}" data-detail="{{ $peran }}"
                data-note="Sesi {{ $peran }} di perangkat ini akan diakhiri." data-ok="Ya, keluar">
                @csrf
                <button type="submit" class="sidebar__link">Keluar akun&nbsp;&nbsp;↗</button>
            </form>
        </aside>

        <div class="workspace">
            <div class="topbar">
                <span class="topbar__crumb">ADMIN / @yield('crumb')</span>
                <span
                    class="topbar__user">{{ auth()->user()->name }}&nbsp;&nbsp;/&nbsp;&nbsp;{{ $peran }}</span>
            </div>

            <div class="workspace__main">
                @if (session('success'))
                    <x-toast type="success" :title="session('success')" text="Data terbaru sudah ditampilkan." />
                @endif
                @if (session('error'))
                    <x-toast type="error" title="Perubahan belum tersimpan" :text="session('error')" />
                @elseif ($errors->any())
                    <x-toast type="error" title="Perubahan belum tersimpan"
                        text="Periksa kolom wajib dan coba lagi." />
                @endif
                @yield('content')
            </div>
        </div>
    </div>
    <x-confirm-modal />
    <script src="{{ asset('js/admin.js') }}" defer></script>
    <script src="{{ asset('js/motion.js') }}" defer></script>
</body>

</html>
