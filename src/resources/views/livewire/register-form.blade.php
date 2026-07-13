<form wire:submit.prevent="register" class="auth-form">
    <div>
        <label for="name">Full name</label>
        <input wire:model="name" id="name" type="text" placeholder="Nama lengkap" required autofocus />
        @error('name')
            <span class="auth-error">{{ $message }}</span>
        @enderror
    </div>

    <div>
        <label for="email">Email address</label>
        <input wire:model="email" id="email" type="email" placeholder="you@example.com" required />
        @error('email')
            <span class="auth-error">{{ $message }}</span>
        @enderror
    </div>

    <div style="margin-top: 1.25rem;">
        <label for="password">Password Baru</label>
        <input wire:model="password" id="password" type="password" placeholder="Minimal 8 karakter" required />
        @error('password')
            <span class="auth-error">{{ $message }}</span>
        @enderror
    </div>

    <div style="margin-top: 1.25rem;">
        <label for="password_confirmation">Confirm Password</label>
        <input wire:model="password_confirmation" id="password_confirmation" type="password" placeholder="Ulangi password" required />
    </div>

    <div style="margin-top: 1.5rem; background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 12px; padding: 15px;">
        <label style="color: #efb16c; font-weight: 700; display: block; margin-bottom: 0.5rem;">Verifikasi Captcha Keamanan</label>
        <div style="display: flex; align-items: center; gap: 15px;">
            <span style="font-size: 1.2rem; font-weight: 800; color: #ffffff; background: rgba(255, 255, 255, 0.05); padding: 8px 15px; border-radius: 8px; min-width: 80px; text-align: center; border: 1px solid rgba(255, 255, 255, 0.08);">
                {{ $num1 }} + {{ $num2 }} =
            </span>
            <input wire:model="captcha_answer" type="text" placeholder="Jawaban" required style="flex: 1; text-align: center; font-size: 1.2rem; font-weight: 800;" />
        </div>
        @error('captcha_answer')
            <span class="auth-error" style="margin-top: 5px; display: block;">{{ $message }}</span>
        @enderror
    </div>

    <div style="margin-top: 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
        <button type="submit" wire:loading.attr="disabled" style="display: flex; align-items: center; justify-content: center; gap: 8px; border: none; border-radius: 999px; padding: 1rem 1.4rem; background: linear-gradient(135deg, #ff4f48 0%, #ffaf6a 100%); color: #08101d; font-weight: 700; cursor: pointer;">
            <span wire:loading.remove>Complete Registration</span>
            <span wire:loading style="display: inline-block; width: 18px; height: 18px; border: 2px solid rgba(255, 255, 255, 0.3); border-radius: 50%; border-top-color: #08101d; animation: spin 1s ease-in-out infinite;"></span>
        </button>
    </div>

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
        Sign up with Google
    </a>

    <style>
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</form>
