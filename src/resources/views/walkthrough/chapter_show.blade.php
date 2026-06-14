<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $chapter->chapter_title }} | {{ $chapter->game->title }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">
    <style>
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
            border-color: #38bdf8;
            background: #1c2d46;
        }

        .guide-header {
            margin-top: 30px;
            padding-bottom: 24px;
            border-bottom: 1px solid #27364b;
        }

        .game-name {
            margin: 0;
            color: #38bdf8;
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
            border-color: #38bdf8;
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
        }
    </style>
</head>
<body>
    <main class="guide">
        <a href="{{ route('games.show', ['slug' => 'persona-3']) }}" class="back-link">
            Back to Story Mission Walkthroughs
        </a>

        <header class="guide-header">
            <p class="game-name">{{ $chapter->game->title }}</p>
            <h1>{{ $chapter->chapter_title }}</h1>
        </header>

        <section class="steps">
            @forelse ($chapter->steps as $step)
                @php
                    $imageUrl = $step->image_url && str_starts_with($step->image_url, 'http')
                        ? $step->image_url
                        : ($step->image_url ? asset($step->image_url) : null);
                @endphp

                <article class="step">
                    <h2>{{ $step->step_title }}</h2>
                    <p>{{ $step->content }}</p>

                    @if ($imageUrl)
                        <img src="{{ $imageUrl }}" alt="{{ $step->step_title }}" loading="lazy">
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
                    <a href="{{ route('persona.story.show', ['mission' => $previousChapter->slug]) }}" class="navigation-link">
                        <span class="navigation-label">&larr; Previous</span>
                        <span class="navigation-title">{{ $previousChapter->chapter_title }}</span>
                    </a>
                @else
                    <span class="navigation-spacer" aria-hidden="true"></span>
                @endif

                @if ($nextChapter)
                    <a href="{{ route('persona.story.show', ['mission' => $nextChapter->slug]) }}" class="navigation-link next">
                        <span class="navigation-label">Next &rarr;</span>
                        <span class="navigation-title">{{ $nextChapter->chapter_title }}</span>
                    </a>
                @endif
            </div>
        </nav>
    </main>
</body>
</html>
