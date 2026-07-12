<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#050812">
    <title>My Account | Walkthrough Game Hub</title>
    @include('partials.favicon')
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
                    Akunmu sudah aktif. Kelola game favorite dan lihat kembali rating yang pernah kamu berikan.
                </p>
            </div>

            <div class="account-identity">
                <img src="{{ $avatarUrl }}" alt="{{ auth()->user()->name }} profile photo" class="account-avatar">
                <div class="account-identity-copy">
                    <strong>{{ auth()->user()->name }}</strong>
                    <span>{{ auth()->user()->email }}</span>
                    <span class="account-role">{{ $roleLabel }}</span>
                </div>
                <div class="account-identity-actions">
                    <button type="button" class="account-action-button primary" data-open-dialog="profile-dialog">
                        <span aria-hidden="true">&#9881;</span>
                        Edit profile
                    </button>
                    <button type="button" class="account-action-button" data-open-dialog="password-dialog">
                        Change password
                    </button>
                </div>
            </div>
        </section>

        @if (session('profile_status') || session('password_status'))
            <div class="account-notice" role="status">
                {{ session('profile_status') ?? session('password_status') }}
            </div>
        @endif

        <section class="member-access">
            <div>
                <p class="account-eyebrow">Your library</p>
                <h2>{{ $favoriteCount }} favorite &middot; {{ $ratingCount }} rating</h2>
                <p>Game yang kamu simpan atau beri rating akan terkumpul di sini.</p>
            </div>
            <a href="{{ route('home') }}#guides" class="hero-button primary">Explore Other Games</a>
        </section>

        <section class="account-section">
            <header class="account-section-heading">
                <div>
                    <p class="account-eyebrow">Saved & rated</p>
                    <h2>Library kamu</h2>
                </div>
            </header>

            <div class="account-game-list">
                @forelse ($libraryGames as $game)
                    @php
                        $personalRating = $game->ratings->first()?->rating;
                    @endphp
                    <a href="{{ route('games.show', ['slug' => $game->route_slug]) }}" class="account-game-item">
                        <img src="{{ $game->cover_url }}" alt="{{ $game->title }} cover" loading="lazy">
                        <div class="account-game-copy">
                            <strong>{{ $game->title }}</strong>
                            @include('partials.rating-stars', [
                                'average' => $game->ratings_avg_rating,
                                'count' => $game->ratings_count,
                            ])
                            <div class="account-game-badges">
                                @if ($game->is_favorited)
                                    <span class="library-badge favorite">Favorited</span>
                                @endif
                                @if ($personalRating)
                                    <span class="library-badge personal">Your rating {{ $personalRating }}/5</span>
                                @endif
                            </div>
                        </div>
                        <span class="account-open-guide">Open walkthrough &rarr;</span>
                    </a>
                @empty
                    <p class="account-empty">Library masih kosong. Favorite atau beri rating pada game untuk menyimpannya di sini.</p>
                @endforelse
            </div>
        </section>

        @include('partials.site-footer')
    </main>

    <dialog class="account-dialog" id="profile-dialog" aria-labelledby="profile-dialog-title">
        <div class="account-dialog-header">
            <div>
                <p class="account-eyebrow">Profile settings</p>
                <h2 id="profile-dialog-title">Edit profile</h2>
            </div>
            <button type="button" class="account-dialog-close" data-close-dialog aria-label="Close profile settings">&times;</button>
        </div>
        <p class="account-dialog-copy">Ubah nama yang tampil dan foto yang muncul di komentar.</p>
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="profile-form">
            @csrf
            @method('PUT')
            <div class="profile-avatar-preview">
                <img src="{{ $avatarUrl }}" alt="Current profile photo" data-avatar-preview>
                <div>
                    <strong>Profile photo</strong>
                    <span>JPG, PNG, atau WebP. Maksimal 2 MB.</span>
                </div>
            </div>
            <label>
                <span>Display name</span>
                <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" autocomplete="name" required>
            </label>
            @error('name', 'profile')
                <p class="profile-error">{{ $message }}</p>
            @enderror
            <label class="profile-file-field">
                <span>Choose a new photo</span>
                <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp" data-avatar-input>
            </label>
            @error('avatar', 'profile')
                <p class="profile-error">{{ $message }}</p>
            @enderror
            <div class="account-dialog-actions">
                <button type="button" class="dialog-button secondary" data-close-dialog>Cancel</button>
                <button type="submit" class="dialog-button primary">Save changes</button>
            </div>
        </form>
    </dialog>

    <dialog class="account-dialog" id="password-dialog" aria-labelledby="password-dialog-title">
        <div class="account-dialog-header">
            <div>
                <p class="account-eyebrow">Security</p>
                <h2 id="password-dialog-title">Change password</h2>
            </div>
            <button type="button" class="account-dialog-close" data-close-dialog aria-label="Close password settings">&times;</button>
        </div>
        <p class="account-dialog-copy">Gunakan minimal 8 karakter dan jangan pakai password yang sama dengan akun lain.</p>
        <form action="{{ route('profile.password.update') }}" method="POST" class="profile-form">
            @csrf
            @method('PUT')
            <label>
                <span>Current password</span>
                <input type="password" name="current_password" autocomplete="current-password" required>
            </label>
            @error('current_password', 'password')
                <p class="profile-error">{{ $message }}</p>
            @enderror
            <label>
                <span>New password</span>
                <input type="password" name="password" autocomplete="new-password" minlength="8" required>
            </label>
            @error('password', 'password')
                <p class="profile-error">{{ $message }}</p>
            @enderror
            <label>
                <span>Confirm new password</span>
                <input type="password" name="password_confirmation" autocomplete="new-password" minlength="8" required>
            </label>
            <div class="account-dialog-actions">
                <button type="button" class="dialog-button secondary" data-close-dialog>Cancel</button>
                <button type="submit" class="dialog-button primary">Update password</button>
            </div>
        </form>
    </dialog>

    <script>
        document.querySelectorAll('[data-open-dialog]').forEach((button) => {
            button.addEventListener('click', () => {
                document.getElementById(button.dataset.openDialog)?.showModal();
            });
        });

        document.querySelectorAll('.account-dialog').forEach((dialog) => {
            dialog.querySelectorAll('[data-close-dialog]').forEach((button) => {
                button.addEventListener('click', () => dialog.close());
            });

            dialog.addEventListener('click', (event) => {
                if (event.target === dialog) dialog.close();
            });
        });

        const avatarInput = document.querySelector('[data-avatar-input]');
        avatarInput?.addEventListener('change', () => {
            const file = avatarInput.files?.[0];
            if (!file) return;

            document.querySelector('[data-avatar-preview]').src = URL.createObjectURL(file);
        });

        @if ($errors->profile->any())
            document.getElementById('profile-dialog')?.showModal();
        @endif

        @if ($errors->password->any())
            document.getElementById('password-dialog')?.showModal();
        @endif
    </script>
</body>
</html>
