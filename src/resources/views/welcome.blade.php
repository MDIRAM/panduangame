<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#070b15">
    <title>Walkthrough Game Hub | Sistem Panduan</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet"/>
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}?v={{ filemtime(public_path('css/welcome.css')) }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="welcome-page" style="background: radial-gradient(circle at top left, #0a1222 0%, #101a36 35%, #070b15 100%); background-color: #070b15;">
    @php
        $spotlightSlides = $games
            ->map(fn ($game) => [
                'label' => $game->chapters_count === 0
                    ? 'Upcoming'
                    : ($game->is_featured ? 'Featured Route' : 'Walkthrough Route'),
                'title' => $game->title,
                'copy' => $game->subtitle ?: $game->description,
                'href' => route('games.show', ['slug' => $game->route_slug]),
                'image' => $game->cover_url,
            ])
            ->values();
    @endphp
    <div class="welcome-shell">
        <div class="guide-layout">
                <aside class="guide-sidebar">
                <a href="/" class="guide-logo" aria-label="PanduanGame home">
                    <img src="{{ asset('coverimg/logogamepanduan.png') }}" alt="PanduanGame logo">
                </a>

                <nav class="guide-nav" aria-label="Main navigation">
                    <a href="/" class="active">Home</a>
                    <a href="#guides">Guides</a>
                    <a href="{{ route('videos.index') }}">Videos</a>
                    @auth
                        @if (auth()->user()->hasRole('super_admin'))
                            <a href="/admin">Admin Panel</a>
                        @else
                            @if (auth()->user()->hasRole('contributor'))
                                <a href="{{ route('contributions.index') }}">Contributor Dashboard</a>
                            @endif
                            <a href="{{ route('dashboard') }}">My Account</a>
                        @endif
                        <form action="{{ route('logout') }}" method="POST" class="guide-nav-logout">
                            @csrf
                            <button type="submit">Log out</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}">Login</a>
                    @endauth
                </nav>
            </aside>

            <main class="guide-main">
                <header class="guide-hero">
                    <div>
                        <p class="brand-label">Walkthrough Game Hub</p>
                        <h1>Find the fastest route through your next game.</h1>
                        <p class="welcome-intro">Cari rute story, strategi boss, dan panduan tamat untuk game favoritmu tanpa harus buka banyak tab.</p>
                    </div>
                    <div class="welcome-actions">
                        <a href="#guides" class="button primary">Browse guides</a>
                        @auth
                            @if (auth()->user()->hasRole('super_admin'))
                                <a href="/admin" class="button secondary">Admin Panel</a>
                            @elseif (auth()->user()->hasRole('contributor'))
                                <a href="{{ route('contributions.create') }}" class="button secondary">Write a guide</a>
                            @else
                                <a href="{{ route('dashboard') }}" class="button secondary">My account</a>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="button secondary">Log in</a>
                        @endauth
                    </div>
                </header>

                @if ($featuredGame)
                    @php
                        $featuredCover = $featuredGame->cover_url;
                    @endphp
                    <section class="spotlight-strip" aria-label="Featured guide" data-spotlight style="--spotlight-image: url('{{ $featuredCover }}');">
                        <div>
                            <span class="hero-tag" data-spotlight-label>Featured Route</span>
                            <h2 data-spotlight-title>{{ $featuredGame->title }}</h2>
                            <p data-spotlight-copy>{{ $featuredGame->subtitle ?: $featuredGame->description }}</p>
                        </div>
                        <a href="{{ route('games.show', ['slug' => $featuredGame->route_slug]) }}" data-spotlight-link>Open guide</a>
                    </section>
                @endif

                <section class="guide-section" id="guides">
                    <div class="section-heading">
                        <div>
                            <p class="section-label">Guides library</p>
                            <h2>Popular game walkthroughs</h2>
                        </div>
                        <span>{{ $games->count() }} available titles</span>
                    </div>

                    <div class="game-guide-grid">
                        @forelse ($games as $game)
                            @php
                                $gameCover = $game->cover_url;
                                $isUpcoming = $game->chapters_count === 0;
                            @endphp
                            <a
                                href="{{ route('games.show', ['slug' => $game->route_slug]) }}"
                                class="guide-game-card {{ $isUpcoming ? 'is-upcoming' : '' }}"
                            >
                                <div class="guide-game-media">
                                    <img src="{{ $gameCover }}" alt="{{ $game->title }} guide cover">
                                </div>
                                <div class="guide-game-copy">
                                    <strong>{{ $game->title }} Guide</strong>
                                    @if ($isUpcoming)
                                        <span class="guide-status upcoming">Upcoming</span>
                                    @else
                                        <span>{{ $game->chapters_count }} walkthrough chapters</span>
                                    @endif
                                </div>
                            </a>
                        @empty
                            <p>Belum ada game yang dipublikasikan.</p>
                        @endforelse
                    </div>
                </section>

                @include('partials.site-footer')
            </main>
        </div>
    </div>
    <script>
        const spotlight = document.querySelector('[data-spotlight]');

        if (spotlight) {
            const title = spotlight.querySelector('[data-spotlight-title]');
            const copy = spotlight.querySelector('[data-spotlight-copy]');
            const link = spotlight.querySelector('[data-spotlight-link]');
            const label = spotlight.querySelector('[data-spotlight-label]');
            const slides = @json($spotlightSlides);
            let activeSlide = 0;

            if (slides.length > 1) {
                window.setInterval(() => {
                activeSlide = (activeSlide + 1) % slides.length;
                const slide = slides[activeSlide];

                spotlight.classList.add('is-changing');

                window.setTimeout(() => {
                    label.textContent = slide.label;
                    title.textContent = slide.title;
                    copy.textContent = slide.copy;
                    link.href = slide.href;
                    spotlight.style.setProperty('--spotlight-image', `url('${slide.image}')`);
                    spotlight.classList.remove('is-changing');
                }, 420);
                }, 4200);
            }
        }
    </script>
</body>
</html>
