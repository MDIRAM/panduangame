<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#070b15">
    <title>Walkthrough Game Hub | Sistem Panduan</title>
    @include('partials.favicon')
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800" rel="stylesheet"/>
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
                <a href="/" class="guide-logo" aria-label="Walkthrough Game Hub home">
                    <span class="guide-logo-mark">
                        <img src="{{ asset('coverimg/wgh-logo.svg') }}" alt="">
                    </span>
                    <span class="guide-logo-text">
                        <strong>Walkthrough</strong>
                        <small>Game Hub</small>
                    </span>
                </a>

                <nav class="guide-nav" aria-label="Main navigation">
                    <a href="/" class="active" data-nav-home>Home</a>
                    <a href="#guides" data-nav-guides>Walkthroughs</a>
                    @auth
                        @if (! auth()->user()->hasRole('super_admin'))
                            <a href="{{ route('dashboard') }}">My Library</a>
                        @endif
                        <form action="{{ route('logout') }}" method="POST" class="guide-nav-logout">
                            @csrf
                            <button type="submit">Log out</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="nav-login">Login</a>
                    @endauth
                </nav>

            </aside>

            <main class="guide-main">
                <header class="guide-hero">
                    <div>
                        <p class="brand-label">Walkthrough Game Hub</p>
                        <h1>Full story walkthroughs, all in one place.</h1>
                        <p class="welcome-intro">Pilih game, buka chapter, lalu lanjutkan story route sampai tamat.</p>
                    </div>
                    @auth
                        <div class="welcome-actions">
                            <a href="{{ route('dashboard') }}" class="button primary">
                                My Library
                            </a>
                        </div>
                    @endauth
                </header>

                @if ($featuredGame)
                    @php
                        $featuredCover = $featuredGame->cover_url;
                    @endphp
                    <section class="spotlight-strip" aria-label="Featured walkthrough" data-spotlight style="--spotlight-image: url('{{ $featuredCover }}');">
                        <div>
                            <span class="hero-tag" data-spotlight-label>Recommended Route</span>
                            <h2 data-spotlight-title>{{ $featuredGame->title }}</h2>
                            <p data-spotlight-copy>{{ $featuredGame->subtitle ?: $featuredGame->description }}</p>
                            <a href="{{ route('games.show', ['slug' => $featuredGame->route_slug]) }}" data-spotlight-link>Open walkthrough</a>
                        </div>
                    </section>
                @endif

                <section class="guide-section" id="guides">
                    <div class="section-heading">
                        <div>
                            <p class="section-label">Walkthrough library</p>
                            <h2>Game walkthroughs</h2>
                        </div>
                        <div class="library-tools">
                            <label class="library-search" for="walkthrough-search">
                                <span>Search</span>
                                <input id="walkthrough-search" type="search" placeholder="Search game..." autocomplete="off" data-guide-search>
                            </label>
                            <span>{{ $games->count() }} available titles</span>
                        </div>
                    </div>

                    <div class="game-guide-grid">
                        @forelse ($games as $game)
                            @php
                                $gameCover = $game->cover_url;
                                $status = $game->chapters_count === 0 ? 'upcoming' : ($game->content_status ?: 'ongoing');
                                $statusLabel = \App\Models\Game::contentStatuses()[$status] ?? 'Ongoing';
                                $isUpcoming = $status === 'upcoming';
                            @endphp
                            <a
                                href="{{ route('games.show', ['slug' => $game->route_slug]) }}"
                                class="guide-game-card {{ $isUpcoming ? 'is-upcoming' : '' }}"
                                data-guide-card
                                data-guide-title="{{ strtolower($game->title) }}"
                            >
                                <div class="guide-game-media">
                                    <img src="{{ $gameCover }}" alt="{{ $game->title }} walkthrough cover">
                                </div>
                                <div class="guide-game-copy">
                                    <strong>{{ $game->title }} Walkthrough</strong>
                                    <span class="guide-status {{ $status }}">{{ $statusLabel }}</span>
                                    @if ($isUpcoming)
                                        <span>Walkthrough belum tersedia.</span>
                                    @else
                                        <span>{{ $game->chapters_count }} walkthrough chapters</span>
                                        @include('partials.rating-stars', [
                                            'average' => $game->ratings_avg_rating,
                                            'count' => $game->ratings_count,
                                        ])
                                        @if ($favoriteGameIds->contains($game->id))
                                            <span class="guide-status favorite">Favorited</span>
                                        @endif
                                    @endif
                                </div>
                            </a>
                        @empty
                            <p>Belum ada game yang dipublikasikan.</p>
                        @endforelse
                    </div>
                    <p class="guide-search-empty" data-guide-search-empty hidden>Game walkthrough tidak ditemukan.</p>
                </section>

                @include('partials.site-footer')
            </main>
        </div>
    </div>
    <script>
        const spotlight = document.querySelector('[data-spotlight]');
        const searchInput = document.querySelector('[data-guide-search]');
        const guideCards = Array.from(document.querySelectorAll('[data-guide-card]'));
        const searchEmpty = document.querySelector('[data-guide-search-empty]');
        const homeNav = document.querySelector('[data-nav-home]');
        const guidesNav = document.querySelector('[data-nav-guides]');
        const guideSection = document.querySelector('#guides');

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

        searchInput?.addEventListener('input', () => {
            const query = searchInput.value.trim().toLowerCase();
            let visibleCount = 0;

            guideCards.forEach((card) => {
                const isVisible = card.dataset.guideTitle.includes(query);
                card.hidden = !isVisible;
                visibleCount += isVisible ? 1 : 0;
            });

            if (searchEmpty) {
                searchEmpty.hidden = visibleCount > 0;
            }
        });

        if (guideSection && homeNav && guidesNav) {
            const setActiveNav = (isGuideActive) => {
                homeNav.classList.toggle('active', !isGuideActive);
                guidesNav.classList.toggle('active', isGuideActive);
            };

            const observer = new IntersectionObserver((entries) => {
                const isGuideVisible = entries.some((entry) => entry.isIntersecting);
                setActiveNav(isGuideVisible || window.location.hash === '#guides');
            }, { threshold: 0.24 });

            observer.observe(guideSection);

            guidesNav.addEventListener('click', () => setActiveNav(true));
            homeNav.addEventListener('click', () => setActiveNav(false));

            if (window.location.hash === '#guides') {
                setActiveNav(true);
            }
        }
    </script>
</body>
</html>
