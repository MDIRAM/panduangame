<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register | Walkthrough Game Hub</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
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
            <form class="auth-form" action="{{ route('register.store') }}" method="POST">
                @csrf
                <div>
                    <label for="name">Full name</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" placeholder="Nama lengkap" required autofocus />
                    @error('name')
                        <span class="auth-error">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="email">Email address</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="you@example.com" required />
                    @error('email')
                        <span class="auth-error">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" placeholder="Minimal 8 karakter" required />
                    @error('password')
                        <span class="auth-error">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="password_confirmation">Confirm password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" placeholder="Ulangi password" required />
                </div>
                <button type="submit">Create account</button>
            </form>
            <div class="auth-foot">
                Sudah punya akun? <a href="/login">Masuk di sini</a>
            </div>
        </aside>
    </div>
</body>
</html>
