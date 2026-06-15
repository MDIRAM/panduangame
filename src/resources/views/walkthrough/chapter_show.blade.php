<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#080d18">
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
            height: 100%;
            background: #080d18;
            color-scheme: dark;
        }

        body {
            height: 100%;
            margin: 0;
            overflow: hidden;
            background:
                linear-gradient(180deg, rgba(24, 55, 91, 0.28), transparent 360px),
                #080d18;
            color: #e8edf5;
            font-family: "Instrument Sans", Arial, sans-serif;
        }

        a {
            color: inherit;
        }

        .walkthrough-shell {
            display: grid;
            grid-template-columns: 300px minmax(0, 1fr);
            height: 100dvh;
            overflow: hidden;
        }

        .chapter-sidebar {
            display: grid;
            grid-template-rows: auto minmax(0, 1fr);
            min-width: 0;
            height: 100dvh;
            overflow: hidden;
            border-right: 1px solid #27364b;
            background: rgba(10, 17, 31, 0.96);
        }

        .sidebar-header {
            padding: 22px 20px 18px;
            border-bottom: 1px solid #27364b;
        }

        .sidebar-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--guide-accent);
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
        }

        .sidebar-header h2 {
            margin: 16px 0 0;
            color: #ffffff;
            font-size: 19px;
            line-height: 1.3;
        }

        .sidebar-header p {
            margin: 6px 0 0;
            color: #8191aa;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .chapter-list {
            min-height: 0;
            height: 100%;
            padding: 14px 12px 28px;
            overflow-x: hidden;
            overflow-y: scroll;
            scrollbar-color: #526078 transparent;
            scrollbar-width: thin;
            touch-action: pan-y;
        }

        .chapter-list:focus-visible {
            outline: 2px solid var(--guide-accent);
            outline-offset: -2px;
        }

        .chapter-group + .chapter-group {
            margin-top: 20px;
        }

        .chapter-group-title {
            margin: 0 8px 8px;
            color: #8191aa;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .chapter-link {
            display: block;
            margin-bottom: 4px;
            padding: 10px 12px;
            border-left: 3px solid transparent;
            border-radius: 4px;
            color: #bdc8d8;
            font-size: 13px;
            font-weight: 600;
            line-height: 1.4;
            text-decoration: none;
            transition: background 160ms ease, border-color 160ms ease, color 160ms ease;
        }

        .chapter-link:hover {
            background: #172235;
            color: #ffffff;
        }

        .chapter-link.active {
            border-left-color: var(--guide-accent);
            background: var(--guide-accent-soft);
            color: #ffffff;
        }

        .guide-scroll {
            min-width: 0;
            height: 100dvh;
            overflow-y: auto;
            scrollbar-color: #526078 transparent;
            scrollbar-width: thin;
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

        .guide-byline {
            margin: 12px 0 0;
            color: #9fb0c8;
            font-size: 14px;
            font-weight: 600;
        }

        .guide-updated {
            margin-left: 8px;
            color: #6f819d;
            font-weight: 500;
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

        .step-content {
            color: #cbd5e1;
            font-size: 17px;
            line-height: 1.8;
        }

        .step-content p {
            margin: 0 0 16px;
        }

        .step-content p:last-child {
            margin-bottom: 0;
        }

        .step-content ul,
        .step-content ol {
            margin: 14px 0;
            padding-left: 28px;
        }

        .step-content li {
            margin: 6px 0;
        }

        .step-content strong {
            color: #f8fafc;
            font-weight: 700;
        }

        .step-content em {
            font-style: italic;
        }

        .step-content a {
            color: var(--guide-accent);
            text-decoration: underline;
            text-underline-offset: 3px;
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

        body.game-dark-souls-2 .chapter-sidebar,
        body.game-elden-ring .chapter-sidebar {
            border-color: #403d32;
            background: rgba(13, 14, 14, 0.97);
        }

        body.game-dark-souls-2 .sidebar-header,
        body.game-elden-ring .sidebar-header {
            border-color: #403d32;
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

        body.game-dark-souls-2 .step-content {
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

        @media (max-width: 860px) {
            html {
                height: auto;
            }

            body {
                height: auto;
                min-height: 100vh;
                overflow: auto;
                overflow-x: hidden;
            }

            .walkthrough-shell {
                display: block;
                width: 100%;
                max-width: 100%;
                height: auto;
                overflow: visible;
            }

            .chapter-sidebar {
                display: block;
                position: relative;
                height: auto;
                overflow: visible;
                border-right: 0;
                border-bottom: 1px solid #27364b;
            }

            .sidebar-header {
                padding: 16px 18px 12px;
            }

            .sidebar-header h2 {
                margin-top: 10px;
                font-size: 17px;
            }

            .chapter-list {
                display: flex;
                gap: 8px;
                width: 100%;
                max-width: 100vw;
                height: auto;
                padding: 10px 14px 14px;
                overflow-x: auto;
                overflow-y: hidden;
                touch-action: pan-x;
            }

            .chapter-group {
                display: contents;
            }

            .chapter-group + .chapter-group {
                margin-top: 0;
            }

            .chapter-group-title {
                display: none;
            }

            .chapter-link {
                flex: 0 0 min(260px, 78vw);
                margin: 0;
                border-left: 0;
                border-bottom: 3px solid transparent;
            }

            .chapter-link.active {
                border-bottom-color: var(--guide-accent);
            }

            .guide-scroll {
                width: 100%;
                max-width: 100%;
                height: auto;
                overflow: visible;
            }
        }

        @media (max-width: 640px) {
            .guide {
                width: auto;
                max-width: 100%;
                margin-right: 12px;
                margin-left: 12px;
                padding-top: 20px;
            }

            h1 {
                font-size: 30px;
                overflow-wrap: anywhere;
            }

            .step {
                max-width: 100%;
                padding: 24px 0 28px;
            }

            .step-content {
                max-width: 100%;
                font-size: 16px;
                overflow-wrap: anywhere;
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

    <div class="walkthrough-shell">
        <aside class="chapter-sidebar">
            <header class="sidebar-header">
                <a href="{{ route('home') }}" class="sidebar-back">
                    <span aria-hidden="true">&larr;</span>
                    Game Library
                </a>
                <h2>Walkthrough Chapters</h2>
                <p>{{ $gameChapters->count() }} chapters</p>
            </header>

            <nav class="chapter-list" aria-label="{{ $chapter->game->title }} chapters" tabindex="0">
                @foreach ($gameChapters->groupBy(fn ($item) => $item->section_title ?: 'Progress Route') as $section => $chapters)
                    <section class="chapter-group">
                        <h3 class="chapter-group-title">{{ $section }}</h3>
                        @foreach ($chapters as $sidebarChapter)
                            <a
                                href="{{ $chapterUrl($sidebarChapter) }}"
                                class="chapter-link {{ $sidebarChapter->is($chapter) ? 'active' : '' }}"
                                @if ($sidebarChapter->is($chapter)) aria-current="page" @endif
                            >
                                {{ $sidebarChapter->chapter_title }}
                            </a>
                        @endforeach
                    </section>
                @endforeach
            </nav>
        </aside>

        <main class="guide-scroll">
            <div class="guide">
                <a href="{{ route('home') }}" class="back-link">
                    Back to Game Library
                </a>

                <header class="guide-header">
                    <p class="game-name">{{ $chapter->game->title }}</p>
                    <h1>{{ $chapter->chapter_title }}</h1>
                    <p class="guide-byline">
                        By Walkthrough Game Hub
                        <span class="guide-updated">Updated {{ $chapter->updated_at->format('M j, Y') }}</span>
                    </p>
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
                            $renderedContent = strip_tags($step->content) === $step->content
                                ? nl2br(e($step->content))
                                : $step->content;
                        @endphp

                        <article class="step {{ filled($step->step_title) ? 'has-title' : 'continuation' }}">
                            @if (filled($step->step_title))
                                <h2>{{ $step->step_title }}</h2>
                            @endif
                            <div class="step-content">{!! $renderedContent !!}</div>

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
            </div>
        </main>
    </div>
    <script>
        const chapterList = document.querySelector('.chapter-list');
        const activeChapter = chapterList?.querySelector('.chapter-link.active');

        if (chapterList && activeChapter) {
            window.requestAnimationFrame(() => {
                if (window.matchMedia('(max-width: 860px)').matches) {
                    chapterList.scrollLeft = activeChapter.offsetLeft
                        - ((chapterList.clientWidth - activeChapter.clientWidth) / 2);
                } else {
                    chapterList.scrollTop = activeChapter.offsetTop
                        - ((chapterList.clientHeight - activeChapter.clientHeight) / 2);
                }
            });
        }

        chapterList?.addEventListener('wheel', (event) => {
            if (window.matchMedia('(max-width: 860px)').matches) {
                if (chapterList.scrollWidth <= chapterList.clientWidth) {
                    return;
                }

                event.preventDefault();
                chapterList.scrollLeft += event.deltaY || event.deltaX;

                return;
            }

            if (chapterList.scrollHeight <= chapterList.clientHeight) {
                return;
            }

            event.preventDefault();
            chapterList.scrollTop += event.deltaY;
        }, { passive: false });
    </script>
</body>
</html>
