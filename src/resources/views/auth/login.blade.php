<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#050812">
    <title>Login | Walkthrough Game Hub</title>
    @include('partials.favicon')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}?v={{ filemtime(public_path('css/auth.css')) }}">
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
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <label class="auth-check" for="remember" style="margin: 0;">
                        <input id="remember" name="remember" type="checkbox" value="1">
                        <span>Remember me</span>
                    </label>
                    <a href="{{ route('password.request') }}" style="color: #efb16c; text-decoration: none; font-size: 0.9rem; font-weight: 600;">Lupa Password?</a>
                </div>
                <button type="submit">Log in</button>
            </form>

            <div class="auth-divider" style="text-align: center; margin: 1.5rem 0; color: rgba(255, 255, 255, 0.3); position: relative; font-size: 0.9rem;">
                <span style="background: #0f182f; padding: 0 12px; position: relative; z-index: 1; border-radius: 999px;">atau</span>
                <div style="position: absolute; top: 50%; left: 0; right: 0; height: 1px; background: rgba(255, 255, 255, 0.1); z-index: 0;"></div>
            </div>

            <a href="{{ route('auth.google') }}" class="google-login-btn" style="display: flex; align-items: center; justify-content: center; gap: 10px; text-decoration: none; padding: 0.95rem 1.4rem; border-radius: 999px; border: 1px solid rgba(255, 255, 255, 0.15); background: rgba(255, 255, 255, 0.05); color: #e9edf7; font-weight: 600; font-size: 0.95rem; transition: background 0.2s ease, border-color 0.2s ease;">
                <svg width="18" height="18" viewBox="0 0 18 18" style="vertical-align: middle;">
                    <path fill="#4285F4" d="M17.64 9.2c0-.63-.06-1.25-.16-1.84H9v3.47h4.84a4.14 4.14 0 0 1-1.8 2.71v2.26h2.9a8.74 8.74 0 0 0 2.7-6.6z"/>
                    <path fill="#34A853" d="M9 18c2.43 0 4.47-.8 5.96-2.2l-2.9-2.26a5.52 5.52 0 0 1-8.09-2.92H1.02v2.33A9 9 0 0 0 9 18z"/>
                    <path fill="#FBBC05" d="M3.97 10.62a5.39 5.39 0 0 1 0-3.24V5.05H1.02a9 9 0 0 0 0 7.9l2.95-2.33z"/>
                    <path fill="#EA4335" d="M9 3.58c1.32 0 2.5.45 3.44 1.35L15 2.4A9 9 0 0 0 1.02 5.05l2.95 2.33A5.48 5.48 0 0 1 9 3.58z"/>
                </svg>
                Sign in with Google
            </a>

            <div class="auth-foot">
                Belum punya akun? <a href="/register">Daftar sekarang</a>
            </div>
        </aside>
    </div>

    <div class="account-shell">
        @include('partials.site-footer')
    </div>
</body>
</html>
