@extends('layouts.public')

@section('title', 'Babarberan Haircuts - Tampil Lebih Rapi, Lebih Percaya Diri')

@section('content')

{{-- ============ TAHAP 1 · Home - Editorial hero ============ --}}
<section id="home" class="container">
    <div class="hero-title">
        <div class="hero-title__meta">
            <span>HAIRCUTS / EST. 2020</span>
            <span>DATANG. DUDUK. RAPI.</span>
        </div>
        <h1 class="hero-title__headline">BABARBERAN</h1>
    </div>

    <div class="hero-composition">
        <div class="hero-intro">
            <p class="hero-intro__tagline">Tampil Lebih Rapi, Lebih Percaya Diri.</p>
            <p class="hero-intro__text">
                Potongan yang pas untuk ritme harimu. Singgah di Babarberan, pilih layanan, dan pulang dengan tampilan yang lebih rapi.
            </p>

            <x-button href="#layanan" icon="arrow-up-right" class="only-desktop">Lihat layanan</x-button>

            <div class="hero-intro__note only-desktop">
                <strong>WALK-IN ONLY</strong>
                <span>{{ $profil?->jam_operasional ?? 'Selasa–Minggu · 13:00–selesai' }}</span>
            </div>
        </div>

        <div class="hero-photo">
            <picture>
                <source media="(max-width: 899px)" srcset="{{ asset('images/hero-mobile.png') }}">
                <img src="{{ asset('images/hero-desktop.png') }}" alt="Pelanggan sedang dipotong rambutnya di Babarberan">
            </picture>
        </div>

        <x-button href="#layanan" icon="arrow-up-right" block class="only-mobile">Lihat layanan</x-button>
    </div>

    <div class="hero-footnote">
        <span>01 / RAPINYA MULAI DI SINI</span>
        <span class="only-desktop">SCROLL UNTUK MENGENAL KAMI ↓</span>
        <span class="only-mobile">WALK-IN ONLY</span>
    </div>
</section>

{{-- ============ Tentang ============ --}}
<section id="tentang">
    <div class="container about">
        <figure class="about__photo">
            <picture class="about__img">
                <source media="(max-width: 899px)" srcset="{{ asset('images/tentang-mobile.png') }}">
                <img src="{{ asset('images/tentang-desktop.png') }}" alt="Kursi barber di ruang Babarberan">
            </picture>
            <figcaption>RUANG UNTUK JEDA / FOTO ILUSTRASI EDITORIAL</figcaption>
        </figure>

        <div class="about__content">
            <div class="about__heading">
                <p class="eyebrow">02 / TENTANG BABARBERAN</p>
                <h2 class="about__title">Bukan sekadar<br>potong rambut.</h2>
            </div>

            <p class="about__text">
                {{ $profil?->cerita ?: 'Babarberan Haircuts didirikan oleh Nurman Taruna pada 25 Desember 2020. Berawal dari keinginan menghadirkan tempat potong rambut yang dekat dengan keseharian, Babarberan mengutamakan hasil yang rapi, suasana santai, dan harga yang mudah dipahami.' }}
            </p>

            <div class="divider"></div>

            <div class="about__facts">
                <div>
                    <span class="k">PEMILIK</span>
                    <span class="v">{{ $profil?->pemilik ?: 'Nurman Taruna' }}</span>
                </div>
                <div>
                    <span class="k">BERDIRI SEJAK</span>
                    <span class="v">{{ $profil?->tanggal_berdiri?->translatedFormat('d F Y') ?? '25 Desember 2020' }}</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="approach">
    <div class="container approach__inner">
        <p class="approach__eyebrow">03 / SEDERHANA, TAPI DIPERHATIKAN</p>

        <div class="approach__list">
            @foreach ([
                ['Rapi, tanpa ribet.',          'Bicarakan potongan yang kamu inginkan sebelum mulai.'],
                ['Nyaman jadi diri sendiri.',   'Suasana santai untuk perubahan kecil yang terasa berarti.'],
                ['Harga yang jelas.',           'Pilih layanan sesuai kebutuhan, lihat harganya sejak awal.'],
            ] as $i => [$judul, $teks])
                <div class="principle">
                    <div class="principle__line"></div>
                    <p class="principle__no">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</p>
                    <p class="principle__title">{{ $judul }}</p>
                    <p class="principle__text">{{ $teks }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============ Layanan ============ --}}
<section id="layanan">
    <div class="container services">
        <div class="services__intro">
            <div class="about__heading">
                <p class="eyebrow">04 / LAYANAN</p>
                <h2 class="about__title">Pilih gaya.<br>Tetap kamu.</h2>
            </div>
            <p class="services__lead">Dari potongan rutin sampai warna baru. Harga sederhana, tanpa perlu menebak.</p>
            @if ($catatanHarga)
                <p class="services__note">{{ $catatanHarga }}</p>
            @endif
        </div>

        <div class="services__list">
            @forelse ($layanan as $i => $l)
                <div class="service-item">
                    <div class="divider"></div>
                    <div class="service-item__row">
                        <span class="service-item__no">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <div class="service-item__details">
                            <p class="service-item__name">{{ $l['nama'] }}</p>
                            @if ($l['deskripsi'])
                                <p class="service-item__desc">{{ $l['deskripsi'] }}</p>
                            @endif
                        </div>
                        <p @class(['service-item__price', 'is-multi' => $l['ganda']])>{{ $l['harga'] }}</p>
                    </div>
                </div>
            @empty
                <p class="services__empty">Daftar layanan akan segera tersedia.</p>
            @endforelse
        </div>
    </div>
</section>

<section class="walkin">
    <div class="container walkin__inner">
        <div class="walkin__invite">
            <p class="walkin__eyebrow">05 / TANPA RESERVASI</p>
            <h2 class="walkin__title">Langsung datang.<br>Kami siap merapikan.</h2>
            <x-button variant="secondary" href="#kontak" icon="arrow-up-right">Informasi kunjungan</x-button>
        </div>

        <div class="walkin__steps">
            @foreach ([
                ['Datang saat jam buka',              $jamKunjungan],
                ['Pilih layanan & ceritakan gayamu',  'Tunjukkan referensi atau diskusikan potonganmu.'],
                ['Ikuti antrean di tempat',           'Layanan dilakukan berdasarkan kedatangan.'],
            ] as $i => [$judul, $teks])
                <div class="visit-step">
                    <span class="visit-step__no">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    <div class="visit-step__body">
                        <p class="visit-step__title">{{ $judul }}</p>
                        <p class="visit-step__text">{{ $teks }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============ Galeri ============ --}}
<section id="galeri">
    <div class="container gallery">
        <div class="gallery__head">
            <div class="gallery__heading">
                <p class="eyebrow">06 / GALERI</p>
                <h2 class="about__title">Detail yang bicara.</h2>
            </div>
            <p class="gallery__lead">Tekstur, garis, dan karakter. Referensi visual untuk menemukan potongan yang terasa seperti dirimu.</p>
        </div>

        <div class="gallery__grid">
            @foreach ([
                [1, '01 / CLEAN LINES',       'Potret samping dengan potongan fade yang bersih'],
                [2, '02 / TEXTURE STUDY',     'Bagian belakang kepala dengan tekstur rambut yang hidup'],
                [3, '03 / THE FINISHING TOUCH', 'Barber merapikan rambut pelanggan dengan gunting'],
            ] as [$no, $caption, $alt])
                <figure class="gallery__item gallery__item--{{ $no }}">
                    <picture class="gallery__img">
                        <source media="(max-width: 899px)" srcset="{{ asset("images/galeri-{$no}-mobile.jpg") }}">
                        <img src="{{ asset("images/galeri-{$no}-desktop.jpg") }}" alt="{{ $alt }}" loading="lazy">
                    </picture>
                    <figcaption>{{ $caption }}</figcaption>
                </figure>
            @endforeach
        </div>

        <div class="gallery__quote">
            <p class="gallery__quote-label">07 / SMALL DETAILS, REAL DIFFERENCE</p>
            <p class="gallery__quote-text">Garis yang bersih. Tekstur yang hidup.<br class="br-desktop"> Tampilan yang tetap terasa natural.</p>
        </div>

        <p class="gallery__disclaimer">Fotografi ilustrasi editorial; bukan dokumentasi hasil pelanggan atau potret pemilik.</p>
    </div>
</section>

{{-- ============ FAQ ============ --}}
<section id="faq">
    <div class="container faq">
        <div class="faq__intro">
            <p class="eyebrow">08 / SEBELUM MAMPIR</p>
            <h2 class="about__title title-tight">Biar lebih jelas.</h2>
        </div>

        <div class="faq__list">
            @foreach ([
                ['Perlu reservasi dulu?',            'Tidak. Babarberan melayani walk-in saja. Langsung datang dan ikuti antrean di tempat.'],
                ['Buka hari apa dan jam berapa?',    $faqJam],
                ['Boleh membawa referensi potongan?', 'Boleh. Tunjukkan foto referensimu dan diskusikan hasil yang kamu inginkan sebelum mulai.'],
            ] as [$tanya, $jawab])
                <div class="faq__item">
                    <p class="faq__q">{{ $tanya }}</p>
                    <p class="faq__a">{{ $jawab }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============ Kontak & kunjungan ============ --}}
<section id="kontak" class="visit">
    <div class="container visit__inner">
        <div class="visit__intro">
            <p class="eyebrow">09 / KONTAK &amp; KUNJUNGAN</p>
            <h2 class="about__title title-tight">Mampir.<br>Bawa gayamu.</h2>
            <p class="visit__text">
                {{ ($waUrl || $igUrl)
                    ? 'Tidak ada pemesanan online. Untuk pertanyaan layanan, hubungi kami lewat kontak berikut.'
                    : 'Tidak ada pemesanan online. Untuk pertanyaan layanan, hubungi kami setelah detail kontak tersedia.' }}
            </p>
            <p class="visit__tag">WALK-IN ONLY{{ ($profil?->senin_tutup ?? true) ? ' / SENIN TUTUP' : '' }}</p>
        </div>

        <div class="visit__list">
            <div class="visit__item">
                <span class="visit__label">ALAMAT</span>
                <p class="visit__value">
                    @if ($profil?->alamat) {!! nl2br(e($profil->alamat)) !!} @else Menunggu detail alamat @endif
                </p>
            </div>

            <div class="visit__item">
                <span class="visit__label">WHATSAPP</span>
                @if ($waUrl)
                    <a class="visit__value" href="{{ $waUrl }}" target="_blank" rel="noopener">{{ $profil->whatsapp }}</a>
                @else
                    <p class="visit__value">Menunggu nomor WhatsApp</p>
                @endif
            </div>

            <div class="visit__item">
                <span class="visit__label">INSTAGRAM</span>
                @if ($igUrl)
                    <a class="visit__value" href="{{ $igUrl }}" target="_blank" rel="noopener">{{ '@' . ltrim($profil->instagram, '@') }}</a>
                @else
                    <p class="visit__value">Menunggu akun Instagram</p>
                @endif
            </div>

            <div class="visit__item">
                <span class="visit__label">JAM BUKA</span>
                <p class="visit__value">{{ $profil?->jam_operasional ?: 'Selasa–Minggu · 13:00–selesai' }}</p>
            </div>
        </div>
    </div>
</section>

{{-- ============ Penutup ============ --}}
<section class="closing">
    <div class="container closing__inner">
        <p class="closing__eyebrow">10 / SAMPAI KETEMU DI KURSI</p>
        <h2 class="closing__title">GAYA BARU.<br>ENERGI BARU.</h2>

        <div class="closing__row">
            <p>Datang langsung saat jam buka.</p>
            <a href="#layanan" aria-label="Lihat layanan"><x-icon name="arrow-up-right" :size="28" stroke="1.25" /></a>
        </div>

        <div class="closing__divider"></div>

        <div class="closing__bar">
            <x-logo on="dark" :href="url('/')" />
            <p class="closing__bar-text">
                {{ $profil?->nama_usaha ?? 'Babarberan Haircuts' }} · Sejak {{ $profil?->tanggal_berdiri?->translatedFormat('d F Y') ?? '25 Desember 2020' }}
            </p>
            <p class="closing__bar-links">
                <a href="{{ url('/') }}">Home</a> / <a href="{{ url('/admin/login') }}">Admin</a>
            </p>
        </div>

        <p class="closing__note">
            {{ $profil?->logo_putih ? '' : 'Logo asli akan ditempatkan saat tersedia. ' }}Fotografi merupakan ilustrasi editorial.
        </p>
    </div>
</section>

@endsection
