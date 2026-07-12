<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#050812">
    <title>Register | Walkthrough Game Hub</title>
    @include('partials.favicon')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}?v={{ filemtime(public_path('css/auth.css')) }}">
    @livewireStyles
</head>
<body class="auth-page">
    <div class="auth-shell">
        <section class="auth-panel">
            <span class="hero-chip">New Player</span>
            <h1>Create your account and save walkthrough favorites.</h1>
            <p>Daftar sekarang untuk menyimpan daftar game, akses rekomendasi walkthrough, dan segera lihat dashboard personalmu.</p>
            <div class="hero-actions">
                <a href="/login" class="hero-button secondary">Already have account</a>
                <a href="/" class="hero-button secondary">Back to home</a>
            </div>
        </section>

        <aside class="auth-card">
            <h2>Create account</h2>
            <p class="auth-note">Buat akun baru untuk masuk ke dashboard walkthrough.</p>
            <livewire:register-form />
            <div class="auth-foot">
                Sudah punya akun? <a href="/login">Masuk di sini</a>
            </div>
        </aside>
    </div>

    <div class="account-shell">
        @include('partials.site-footer')
    </div>
    @livewireScripts
</body>
</html>
