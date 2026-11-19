<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk Admin - Babarberan Haircuts</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/babarberan.css') }}">
</head>
<body>
<div class="login">

    <aside class="login__editorial">
        <p class="login__eyebrow">BABARBERAN HAIRCUTS / RUANG ADMIN</p>
        <h2 class="login__headline">DI BALIK<br>TAMPILAN<br>YANG RAPI.</h2>
        <div class="login__photo">
            <img src="{{ asset('images/login-barber-tools.png') }}" alt="">
        </div>
        <p class="login__caption">Kelola profil, layanan, dan catatan transaksi dalam satu ruang kerja yang fokus.</p>
    </aside>

    <main class="login__panel">
        <div class="login__header">
            <x-logo :href="url('/')" />
            <a href="{{ url('/') }}" class="login__back">← Website</a>
        </div>

        <form method="POST" action="{{ route('login.post') }}" class="login__form" novalidate>
            @csrf

            <div class="login__heading">
                <p class="login__kicker">AKSES KHUSUS ADMIN</p>
                <h1 class="login__title">Masuk admin.</h1>
                <p class="login__lead">Gunakan akun admin untuk mengelola Babarberan Haircuts.</p>
            </div>

            <div class="field @if ($errors->any()) has-error @endif">
                <label class="field__label" for="username">Nama pengguna</label>
                <input class="field__control" id="username" name="username" type="text"
                       value="{{ old('username') }}" placeholder="admin.demo"
                       autocomplete="username" autofocus>
            </div>

            <div class="field @if ($errors->any()) has-error @endif">
                <label class="field__label" for="password">Kata sandi</label>
                <input class="field__control" id="password" name="password" type="password"
                       autocomplete="current-password">
            </div>

            @if ($errors->any())
                <div class="inline-error" role="alert">
                    <x-icon name="circle-alert" :size="18" />
                    <p>{{ $errors->first() }}</p>
                </div>
            @endif

            <x-button type="submit" icon="arrow-right" block>Masuk</x-button>
        </form>

    </main>

</div>
</body>
</html>