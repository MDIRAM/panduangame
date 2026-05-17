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
            <p class="auth-note">Ini hanya halaman UI. Belum ada koneksi login / register DB.</p>
            <form class="auth-form" action="#" method="POST">
                <div>
                    <label for="name">Full name</label>
                    <input id="name" name="name" type="text" placeholder="Nama lengkap" />
                </div>
                <div>
                    <label for="email">Email address</label>
                    <input id="email" name="email" type="email" placeholder="you@example.com" />
                </div>
                <div>
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" placeholder="********" />
                </div>
                <div>
                    <label for="password_confirmation">Confirm password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" placeholder="********" />
                </div>
                <button type="button">Create account</button>
            </form>
            <div class="auth-foot">
                Sudah punya akun? <a href="/login">Masuk di sini</a>
            </div>
        </aside>
    </div>
</body>
</html>
