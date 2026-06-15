<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $game->title }} | Walkthrough Game Hub</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="welcome-page" style="background: radial-gradient(circle at top left, #0a1222 0%, #101a36 35%, #070b15 100%); background-color: #070b15;">
    @php
        $slug = $game->route_slug;
        $coverUrl = $game->cover_url;
        $coverPosition = $game->slug === 'dark-souls-2' ? 'center bottom' : 'center';
    @endphp

    <div class="welcome-shell">
        <main class="game-page-hero">
            <div class="welcome-header">
                <div class="game-hero-top">
                    <p class="brand-label">Walkthrough Game Hub</p>
                    <h1>{{ $game->title }}</h1>
                    <p class="welcome-intro">{{ $game->subtitle }}</p>
                </div>

                <div class="welcome-actions">
                    <a href="{{ url('/') }}" class="button secondary">Back to homepage</a>
                </div>
            </div>

            <div style="border-radius:20px;overflow:hidden;margin-top:1rem;">
                <div style="height:380px;background-image:url('{{ $coverUrl }}');background-size:cover;background-position:{{ $coverPosition }};"></div>
            </div>

            @if (in_array($game->slug, ['dark-souls-2', 'elden-ring'], true))
                @php
                    $isEldenRing = $game->slug === 'elden-ring';
                @endphp
                <section class="wiki-route-page">
                    <header class="wiki-route-header">
                        <p>{{ $isEldenRing ? 'Elden Ring Guide' : 'Dark Souls 2 Wiki Guide' }}</p>
                        <h2>{{ $isEldenRing ? 'The Lands Between Walkthrough' : 'Game Progress Route' }}</h2>
                        <span>
                            {{ $isEldenRing
                                ? 'Rute awal dari Limgrave menuju Stormveil Castle, lengkap dengan upgrade, NPC, dan boss utama.'
                                : 'Urutan area awal yang direkomendasikan untuk playthrough pertama. Seluruh data berasal dari database.' }}
                        </span>
                    </header>

                    @if ($game->chapters->isNotEmpty())
                        <nav class="wiki-route-toc" aria-label="Progress route navigation">
                            @foreach ($game->chapters as $chapter)
                                <a href="#{{ $chapter->slug }}">{{ $chapter->chapter_title }}</a>
                            @endforeach
                        </nav>

                        @foreach ($game->chapters as $chapter)
                            @php
                                $previewImage = $chapter->cover_image
                                    ?? $chapter->overview_image
                                    ?? $chapter->steps->firstWhere('image_url')?->image_url;
                                $previewImageUrl = $previewImage && str_starts_with($previewImage, 'http')
                                    ? $previewImage
                                    : ($previewImage ? asset($previewImage) : null);
                            @endphp

                            <article class="wiki-route-entry" id="{{ $chapter->slug }}">
                                <div class="wiki-route-media">
                                    <h3>{{ $chapter->chapter_title }}</h3>

                                    @if ($previewImageUrl)
                                        <img src="{{ $previewImageUrl }}" alt="{{ $chapter->chapter_title }} area" loading="lazy">
                                    @else
                                        <div class="wiki-route-image-placeholder">{{ $chapter->chapter_title }}</div>
                                    @endif
                                </div>

                                <div class="wiki-route-copy">
                                    @if (filled($chapter->overview))
                                        @foreach (array_slice($chapter->overview, 0, 2) as $paragraph)
                                            <p>{{ $paragraph }}</p>
                                        @endforeach
                                    @else
                                        @foreach ($chapter->steps->take(2) as $step)
                                            <p>{{ $step->content }}</p>
                                        @endforeach
                                    @endif

                                    <a
                                        href="{{ route('games.walkthrough.show', ['gameSlug' => $game->route_slug, 'chapterSlug' => $chapter->slug]) }}"
                                        class="button secondary"
                                    >
                                        Buka Area
                                    </a>
                                </div>
                            </article>
                        @endforeach

                        @unless ($isEldenRing)
                            <section class="wiki-map-section">
                                <h2>World Inter-connectivity</h2>
                                <p>Klik peta untuk memperbesar.</p>
                                <a href="{{ asset('coverimg/DS2/DS2map2.jpg') }}" target="_blank" rel="noopener">
                                    <img src="{{ asset('coverimg/DS2/DS2map2.jpg') }}" alt="Dark Souls 2 world inter-connectivity map">
                                </a>
                            </section>
                        @endunless
                    @else
                        <div class="wiki-route-image-placeholder">
                            Data {{ $game->title }} belum tersedia. Jalankan seeder game terkait.
                        </div>
                    @endif
                </section>
            @elseif ($game->slug === 'persona-3-reload')
                <section class="persona-story-page">
                    <header class="persona-story-header">
                        <h2>Story Mission Walkthroughs</h2>
                        <p>Pilih misi untuk membuka langkah walkthrough dan gambar yang tersimpan di database.</p>
                    </header>

                    <div class="persona-month-list">
                        @forelse ($game->chapters->groupBy('section_title') as $month => $missions)
                            <section class="persona-month-section" id="persona-{{ str($month)->lower() }}">
                                <h3>{{ $month }}</h3>
                                <ul>
                                    @foreach ($missions as $mission)
                                        <li>
                                            <a href="{{ route('persona.story.show', ['mission' => $mission->slug]) }}">
                                                {{ $mission->chapter_title }}
                                            </a>
                                            <span class="persona-status {{ $mission->steps->isNotEmpty() ? 'ready' : 'planned' }}">
                                                {{ $mission->steps->isNotEmpty() ? $mission->steps->count().' steps' : 'Planned' }}
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </section>
                        @empty
                            <p>Data Persona 3 belum tersedia. Jalankan migration dan GameSeeder.</p>
                        @endforelse
                    </div>
                </section>
            @else
                <section class="persona-story-page">
                    <header class="persona-story-header">
                        <h2>Walkthrough Dalam Persiapan</h2>
                        <p>{{ $game->description }}</p>
                    </header>
                </section>
            @endif

            @if ($game->walkthroughContributions->isNotEmpty())
                <section class="persona-story-page">
                    <header class="persona-story-header">
                        <h2>Community Walkthroughs</h2>
                        <p>Panduan buatan pemain yang sudah direview dan dipublikasikan.</p>
                    </header>

                    <div class="persona-month-list">
                        <section class="persona-month-section">
                            <ul>
                                @foreach ($game->walkthroughContributions as $contribution)
                                    <li>
                                        <a href="{{ route('contributions.show', $contribution) }}">
                                            {{ $contribution->title }}
                                        </a>
                                        <span class="persona-status ready">
                                            {{ $contribution->steps_count }} steps · {{ $contribution->author->name }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </section>
                    </div>
                </section>
            @endif
        </main>
    </div>
</body>
</html>
