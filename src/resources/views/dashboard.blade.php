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
                    Akunmu sudah aktif. Kelola game favorite dan lihat kembali rating yang pernah kamu berikan.
                </p>
            </div>

            <div class="account-identity">
                <span>Signed in as</span>
                <strong>{{ auth()->user()->email }}</strong>
                <span class="account-role">{{ $roleLabel }}</span>
            </div>
        </section>

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
</body>
</html>
