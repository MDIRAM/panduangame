<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#050812">
    <title>My Account | Walkthrough Game Hub</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}?v={{ filemtime(public_path('css/auth.css')) }}">
</head>
<body class="auth-page">
    <main class="account-shell">
        <header class="account-topbar">
            <a href="{{ route('home') }}" class="account-brand">Walkthrough Game Hub</a>
            <form action="{{ route('logout') }}" method="POST" class="logout-form">
                @csrf
                <button type="submit">Log out</button>
            </form>
        </header>

        <section class="account-header">
            <div>
                <span class="hero-chip">My Account</span>
                <h1>Halo, {{ auth()->user()->name }}.</h1>
                <p>
                    {{ $isContributor
                        ? 'Akunmu berstatus Contributor. Kamu bisa membuat walkthrough dari halaman Contributor Dashboard.'
                        : 'Akunmu sudah aktif dan dapat digunakan untuk membaca seluruh walkthrough di katalog.' }}
                </p>
            </div>

            <div class="account-identity">
                <span>Signed in as</span>
                <strong>{{ auth()->user()->email }}</strong>
                <span class="account-role">{{ $isContributor ? 'Contributor' : 'Member' }}</span>
            </div>
        </section>

        @if ($isContributor)
            <section class="member-access">
                <div>
                    <p class="account-eyebrow">Current access</p>
                    <h2>Akun Contributor</h2>
                    <p>
                        Kamu bisa menulis walkthrough dari frontend. Semua draft, status review,
                        dan guide yang kamu buat dikelola di Contributor Dashboard.
                    </p>
                </div>
                <a href="{{ route('contributions.index') }}" class="hero-button primary">Contributor Dashboard</a>
            </section>
        @else
            <section class="member-access">
                <div>
                    <p class="account-eyebrow">Current access</p>
                    <h2>Akun Member</h2>
                    <p>
                        Kamu dapat membaca semua guide dan menyimpan akun untuk akses berikutnya.
                        Fitur menulis walkthrough akan tersedia setelah admin mengubah role akunmu menjadi Contributor.
                    </p>
                </div>
                <a href="{{ route('home') }}" class="hero-button primary">Browse Guides</a>
            </section>
        @endif

        @include('partials.site-footer')
    </main>
</body>
</html>
