<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $game['title'] }} | Walkthrough Game Hub</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="welcome-page" style="background: radial-gradient(circle at top left, #0a1222 0%, #101a36 35%, #070b15 100%); background-color: #070b15;">
    @php
        $detailCovers = [
            'elden-ring' => asset('coverimg/EldenRing.png'),
            'dark-souls-2' => asset('coverimg/Dark_Souls_2.jpg'),
        ];

        $coverUrl = $detailCovers[$slug] ?? route('cover', ['slug' => $slug]);
        $coverPosition = $slug === 'dark-souls-2' ? 'center bottom' : 'center';
    @endphp

    <div class="welcome-shell">
        <main class="game-page-hero">
            <div class="welcome-header">
                <div class="game-hero-top">
                    <p class="brand-label">Walkthrough Game Hub</p>
                    <h1>{{ $game['title'] }}</h1>
                    <p class="welcome-intro">{{ $game['subtitle'] }}</p>
                </div>

                <div class="welcome-actions">
                    <a href="{{ url('/') }}" class="button secondary">Back to homepage</a>
                </div>
            </div>

            <div style="border-radius:20px;overflow:hidden;margin-top:1rem;">
                <div style="height:380px;background-image:url('{{ $coverUrl }}');background-size:cover;background-position:{{ $coverPosition }};"></div>
            </div>

            @if ($slug === 'dark-souls-2')
                <section class="wiki-route-page">
                    <header class="wiki-route-header">
                        <p>Dark Souls 2 Wiki Guide</p>
                        <h2>Game Progress Route</h2>
                        <span>Urutan area awal yang direkomendasikan untuk playthrough pertama. Seluruh data berasal dari database.</span>
                    </header>

                    @if ($databaseGame && $databaseGame->chapters->isNotEmpty())
                        <nav class="wiki-route-toc" aria-label="Progress route navigation">
                            @foreach ($databaseGame->chapters as $chapter)
                                <a href="#{{ $chapter->slug }}">{{ $chapter->chapter_title }}</a>
                            @endforeach
                        </nav>

                        @foreach ($databaseGame->chapters as $chapter)
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
                                        href="{{ route('games.walkthrough.show', ['gameSlug' => $databaseGame->slug, 'chapterSlug' => $chapter->slug]) }}"
                                        class="button secondary"
                                    >
                                        Buka Area
                                    </a>
                                </div>
                            </article>
                        @endforeach

                        <section class="wiki-map-section">
                            <h2>World Inter-connectivity</h2>
                            <p>Klik peta untuk memperbesar.</p>
                            <a href="{{ asset('coverimg/DS2/DS2map2.jpg') }}" target="_blank" rel="noopener">
                                <img src="{{ asset('coverimg/DS2/DS2map2.jpg') }}" alt="Dark Souls 2 world inter-connectivity map">
                            </a>
                        </section>
                    @else
                        <div class="wiki-route-image-placeholder">
                            Data Dark Souls 2 belum tersedia. Jalankan DarkSouls2Seeder.
                        </div>
                    @endif
                </section>
            @elseif ($slug === 'persona-3')
                <section class="persona-story-page">
                    <header class="persona-story-header">
                        <h2>Story Mission Walkthroughs</h2>
                        <p>Pilih misi untuk membuka langkah walkthrough dan gambar yang tersimpan di database.</p>
                    </header>

                    <div class="persona-month-list">
                        @forelse ($personaChapters->groupBy('section_title') as $month => $missions)
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
                        <p>{{ $game['description'] }}</p>
                    </header>
                </section>
            @endif
        </main>
    </div>
</body>
</html>
