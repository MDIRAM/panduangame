<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#050812">
    <title>Setel Ulang Password | Walkthrough Game Hub</title>
    @include('partials.favicon')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}?v={{ filemtime(public_path('css/auth.css')) }}">
</head>
<body class="auth-page">
    <div class="auth-shell">
        <section class="auth-panel">
            <span class="hero-chip">Reset Portal</span>
            <h1>Set a new password.</h1>
            <p>Silakan buat password baru yang aman untuk akun Anda agar bisa kembali mengakses progress walkthrough game Anda.</p>
        </section>

        <aside class="auth-card">
            <h2>Reset Password</h2>
            <p class="auth-note">Masukkan password baru Anda.</p>

            <form class="auth-form" action="{{ route('password.update') }}" method="POST">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                
                <div>
                    <label for="email">Email address</label>
                    <input id="email" name="email" type="email" value="{{ old('email', request()->email) }}" placeholder="you@example.com" required autofocus />
                    @error('email')
                        <span class="auth-error">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="password">Password Baru</label>
                    <input id="password" name="password" type="password" placeholder="Minimal 8 karakter" required />
                    @error('password')
                        <span class="auth-error">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="password_confirmation">Confirm Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" placeholder="Ulangi password" required />
                </div>
                <button type="submit">Reset Password</button>
            </form>
        </aside>
    </div>

    <div class="account-shell">
        @include('partials.site-footer')
    </div>
</body>
</html>
