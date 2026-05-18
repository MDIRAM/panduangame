<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Walkthrough Game Hub</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body class="auth-page">
    <div class="auth-shell">
        <section class="auth-panel">
            <span class="hero-chip">Access Portal</span>
            <h1>Login and continue your walkthrough journey.</h1>
            <p>Masuk ke akunmu untuk melihat panduan terbaru, rekomendasi walkthrough, dan statistik progress game.</p>
            <div class="hero-actions">
                <a href="/register" class="hero-button secondary">Create account</a>
                <a href="/" class="hero-button secondary">Back to home</a>
            </div>
        </section>

        <aside class="auth-card">
            <h2>Sign in</h2>
            <p class="auth-note">Masuk dengan email dan password yang sudah terdaftar.</p>
            <form class="auth-form" action="{{ route('login.store') }}" method="POST">
                @csrf
                <div>
                    <label for="email">Email address</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="you@example.com" required autofocus />
                    @error('email')
                        <span class="auth-error">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" placeholder="********" required />
                    @error('password')
                        <span class="auth-error">{{ $message }}</span>
                    @enderror
                </div>
                <label class="auth-check" for="remember">
                    <input id="remember" name="remember" type="checkbox" value="1">
                    <span>Remember me</span>
                </label>
                <button type="submit">Log in</button>
            </form>
            <div class="auth-foot">
                Belum punya akun? <a href="/register">Daftar sekarang</a>
            </div>
        </aside>
    </div>
</body>
</html>
