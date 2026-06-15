<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $chapter->chapter_title }} | {{ $chapter->game->title }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">
    <style>
        :root {
            --guide-accent: {{ in_array($chapter->game->slug, ['dark-souls-2', 'elden-ring'], true) ? '#d9b45b' : '#38bdf8' }};
            --guide-accent-soft: {{ in_array($chapter->game->slug, ['dark-souls-2', 'elden-ring'], true) ? '#27271f' : '#1c2d46' }};
        }

        * {
            box-sizing: border-box;
        }

        html {
            background: #080d18;
        }

        body {
            min-height: 100vh;
            margin: 0;
            background:
                linear-gradient(180deg, rgba(24, 55, 91, 0.28), transparent 360px),
                #080d18;
            color: #e8edf5;
            font-family: "Instrument Sans", Arial, sans-serif;
        }

        a {
            color: inherit;
        }

        .guide {
            width: min(100% - 32px, 980px);
            margin: 0 auto;
            padding: 34px 0 72px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            min-height: 40px;
            padding: 0 15px;
            border: 1px solid #31425c;
            border-radius: 6px;
            background: #172235;
            color: #dbeafe;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            transition: background 160ms ease, border-color 160ms ease;
        }

        .back-link:hover {
            border-color: var(--guide-accent);
            background: var(--guide-accent-soft);
        }

        .guide-header {
            margin-top: 30px;
            padding-bottom: 24px;
            border-bottom: 1px solid #27364b;
        }

        .game-name {
            margin: 0;
            color: var(--guide-accent);
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
        }

        h1 {
            max-width: 850px;
            margin: 10px 0 0;
            color: #ffffff;
            font-size: clamp(30px, 5vw, 46px);
            line-height: 1.12;
        }

        .steps {
            margin-top: 12px;
        }

        .step {
            padding: 30px 0 34px;
            border-bottom: 1px solid #27364b;
        }

        .step h2 {
            margin: 0 0 12px;
            color: #f8fafc;
            font-size: 21px;
            line-height: 1.35;
        }

        .step p {
            margin: 0;
            color: #cbd5e1;
            font-size: 17px;
            line-height: 1.8;
        }

        .step img {
            display: block;
            width: min(100%, 780px);
            height: auto;
            margin-top: 22px;
            border: 1px solid #334155;
            border-radius: 6px;
            background: #111827;
        }

        body.game-dark-souls-2 .steps {
            margin-top: 6px;
        }

        body.game-dark-souls-2 {
            background:
                linear-gradient(180deg, rgba(112, 84, 37, 0.14), transparent 420px),
                #0c0d0d;
        }

        body.game-elden-ring {
            background:
                linear-gradient(180deg, rgba(127, 104, 49, 0.2), transparent 460px),
                #090b0b;
        }

        body.game-elden-ring .guide-header,
        body.game-elden-ring .chapter-overview,
        body.game-elden-ring .step {
            border-color: #403d32;
        }

        body.game-elden-ring .chapter-overview-media h2 {
            color: var(--guide-accent);
            font-family: Georgia, "Times New Roman", serif;
        }

        body.game-elden-ring .step h2 {
            font-family: Georgia, "Times New Roman", serif;
            font-size: 24px;
        }

        body.game-elden-ring .step img {
            width: min(100%, 700px);
            margin-right: auto;
            margin-left: auto;
            border-color: #514b3b;
        }

        body.game-dark-souls-2 .guide-header {
            border-bottom-color: #414039;
        }

        .chapter-overview {
            display: grid;
            grid-template-columns: minmax(220px, 34%) minmax(0, 1fr);
            gap: 28px;
            margin-top: 30px;
            padding: 26px 0 32px;
            border-bottom: 1px solid #414039;
        }

        .chapter-overview-media h2 {
            margin: 0 0 16px;
            color: var(--guide-accent);
            font-family: Georgia, "Times New Roman", serif;
            font-size: 19px;
            font-weight: 400;
        }

        .chapter-overview-media img {
            display: block;
            width: 100%;
            aspect-ratio: 16 / 9;
            border: 1px solid #4a463a;
            border-radius: 2px;
            object-fit: cover;
        }

        .chapter-overview-copy p {
            margin: 0 0 13px;
            color: #d2d0cb;
            font-size: 16px;
            line-height: 1.55;
        }

        .chapter-overview-copy p:last-child {
            margin-bottom: 0;
        }

        body.game-dark-souls-2 .step {
            padding: 14px 0;
            border-bottom: 0;
        }

        body.game-dark-souls-2 .step.has-title {
            margin-top: 24px;
            padding-top: 34px;
            border-top: 1px solid #394252;
        }

        body.game-dark-souls-2 .step.has-title:first-child {
            margin-top: 0;
            border-top: 0;
        }

        body.game-dark-souls-2 .step h2 {
            font-family: Georgia, "Times New Roman", serif;
            font-size: 25px;
        }

        body.game-dark-souls-2 .step p {
            color: #d6d9df;
            line-height: 1.75;
        }

        body.game-dark-souls-2 .step img {
            width: min(100%, 620px);
            margin-right: auto;
            margin-left: auto;
        }

        .empty {
            margin-top: 30px;
            padding: 24px;
            border: 1px solid #31425c;
            border-radius: 6px;
            background: #111a2a;
        }

        .empty h2 {
            margin: 0;
            color: #ffffff;
            font-size: 22px;
        }

        .empty p {
            margin: 8px 0 0;
            color: #aebbd0;
            line-height: 1.7;
        }

        .chapter-navigation {
            margin-top: 38px;
            padding-top: 26px;
            border-top: 1px solid #31425c;
        }

        .chapter-navigation h2 {
            margin: 0;
            color: #f8fafc;
            font-size: 20px;
            line-height: 1.4;
        }

        .navigation-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
            margin-top: 18px;
        }

        .navigation-link {
            display: flex;
            min-height: 96px;
            flex-direction: column;
            justify-content: center;
            padding: 17px 20px;
            border: 1px solid #526078;
            border-radius: 8px;
            background: #111a2a;
            text-decoration: none;
            transition: border-color 160ms ease, background 160ms ease;
        }

        .navigation-link:hover {
            border-color: var(--guide-accent);
            background: #17243a;
        }

        .navigation-link.next {
            text-align: right;
        }

        .navigation-label {
            color: #ffffff;
            font-size: 17px;
            font-weight: 700;
        }

        .navigation-title {
            margin-top: 5px;
            overflow-wrap: anywhere;
            color: #9fb0c8;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .navigation-spacer {
            min-height: 1px;
        }

        @media (max-width: 640px) {
            .guide {
                width: min(100% - 24px, 980px);
                padding-top: 20px;
            }

            .step {
                padding: 24px 0 28px;
            }

            .step p {
                font-size: 16px;
            }

            .navigation-grid {
                grid-template-columns: 1fr;
            }

            .navigation-link.next {
                text-align: left;
            }

            .chapter-overview {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .chapter-overview-media img {
                width: min(100%, 520px);
            }
        }
    </style>
</head>
<body class="game-{{ $chapter->game->slug }}">
    @php
        $isPersona = $chapter->game->slug === 'persona-3-reload';
        $gamePageSlug = $chapter->game->route_slug;
        $chapterUrl = static fn ($item) => $isPersona
            ? route('persona.story.show', ['mission' => $item->slug])
            : route('games.walkthrough.show', [
                'gameSlug' => $chapter->game->route_slug,
                'chapterSlug' => $item->slug,
            ]);
        $overviewImageUrl = $chapter->overview_image && str_starts_with($chapter->overview_image, 'http')
            ? $chapter->overview_image
            : ($chapter->overview_image ? asset($chapter->overview_image) : null);
    @endphp

    <main class="guide">
        <a href="{{ route('games.show', ['slug' => $gamePageSlug]) }}" class="back-link">
            @if ($isPersona)
                Back to Story Mission Walkthroughs
            @elseif ($chapter->game->slug === 'dark-souls-2')
                Back to Game Progress Route
            @else
                Back to {{ $chapter->game->title }} Walkthrough
            @endif
        </a>

        <header class="guide-header">
            <p class="game-name">{{ $chapter->game->title }}</p>
            <h1>{{ $chapter->chapter_title }}</h1>
        </header>

        @if (filled($chapter->overview))
            <section class="chapter-overview" aria-label="{{ $chapter->chapter_title }} overview">
                <div class="chapter-overview-media">
                    <h2>{{ $chapter->chapter_title }}</h2>
                    @if ($overviewImageUrl)
                        <img src="{{ $overviewImageUrl }}" alt="{{ $chapter->chapter_title }}" loading="eager">
                    @endif
                </div>

                <div class="chapter-overview-copy">
                    @foreach ($chapter->overview as $paragraph)
                        <p>{{ $paragraph }}</p>
                    @endforeach
                </div>
            </section>
        @endif

        <section class="steps">
            @forelse ($chapter->steps as $step)
                @php
                    $imageUrl = $step->image_url && str_starts_with($step->image_url, 'http')
                        ? $step->image_url
                        : ($step->image_url ? asset($step->image_url) : null);
                @endphp

                <article class="step {{ filled($step->step_title) ? 'has-title' : 'continuation' }}">
                    @if (filled($step->step_title))
                        <h2>{{ $step->step_title }}</h2>
                    @endif
                    <p>{{ $step->content }}</p>

                    @if ($imageUrl)
                        <img src="{{ $imageUrl }}" alt="{{ $step->step_title ?: $chapter->chapter_title }}" loading="lazy">
                    @endif
                </article>
            @empty
                <div class="empty">
                    <h2>Konten belum tersedia</h2>
                    <p>Data walkthrough untuk misi ini belum diimpor ke database.</p>
                </div>
            @endforelse
        </section>

        <nav class="chapter-navigation" aria-label="Walkthrough navigation">
            @if ($nextChapter)
                <h2>Up Next: {{ $nextChapter->chapter_title }}</h2>
            @elseif ($previousChapter)
                <h2>Walkthrough Navigation</h2>
            @endif

            <div class="navigation-grid">
                @if ($previousChapter)
                    <a href="{{ $chapterUrl($previousChapter) }}" class="navigation-link">
                        <span class="navigation-label">&larr; Previous</span>
                        <span class="navigation-title">{{ $previousChapter->chapter_title }}</span>
                    </a>
                @else
                    <span class="navigation-spacer" aria-hidden="true"></span>
                @endif

                @if ($nextChapter)
                    <a href="{{ $chapterUrl($nextChapter) }}" class="navigation-link next">
                        <span class="navigation-label">Next &rarr;</span>
                        <span class="navigation-title">{{ $nextChapter->chapter_title }}</span>
                    </a>
                @endif
            </div>
        </nav>
    </main>
</body>
</html>
