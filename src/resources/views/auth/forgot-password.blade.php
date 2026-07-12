<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#050812">
    <title>Lupa Password | Walkthrough Game Hub</title>
    @include('partials.favicon')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}?v={{ filemtime(public_path('css/auth.css')) }}">
</head>
<body class="auth-page">
    <div class="auth-shell">
        <section class="auth-panel">
            <span class="hero-chip">Reset Portal</span>
            <h1>Forgot your password? No problem.</h1>
            <p>Masukkan alamat email Anda, dan kami akan mengirimkan link untuk menyetel ulang password Anda ke password yang baru.</p>
            <div class="hero-actions">
                <a href="/login" class="hero-button secondary">Back to login</a>
            </div>
        </section>

        <aside class="auth-card">
            <h2>Reset Password</h2>
            <p class="auth-note">Kirim email pemulihan akun.</p>

            @if (session('status'))
                <div style="background: rgba(52, 168, 83, 0.15); border: 1px solid #34a853; color: #a3e2b4; padding: 10px; border-radius: 12px; font-size: 0.9rem; text-align: center; margin-bottom: 1rem;">
                    Link reset password telah kami kirimkan ke email Anda!
                </div>
            @endif

            <form class="auth-form" action="{{ route('password.email') }}" method="POST">
                @csrf
                <div>
                    <label for="email">Email address</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="you@example.com" required autofocus />
                    @error('email')
                        <span class="auth-error">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit">Kirim Link Reset</button>
            </form>
        </aside>
    </div>

    <div class="account-shell">
        @include('partials.site-footer')
    </div>
</body>
</html>
