<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#080d18">
    <title>{{ $contribution->title }} | {{ $contribution->game->title }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/contributions.css') }}?v={{ filemtime(public_path('css/contributions.css')) }}">
</head>
<body class="contribution-page">
    <main class="contribution-shell">
        <nav class="contribution-topbar">
            <a href="{{ route('games.show', $contribution->game->route_slug) }}" class="button">
                Kembali ke {{ $contribution->game->title }}
            </a>
        </nav>

        <header class="public-guide-header">
            <p class="eyebrow">Community walkthrough</p>
            <h1>{{ $contribution->title }}</h1>
            <p class="contribution-meta">
                {{ $contribution->game->title }} · By {{ $contribution->author->name }}
            </p>
            <p class="public-guide-summary">{{ $contribution->summary }}</p>
        </header>

        <section class="public-steps">
            @foreach ($contribution->steps as $step)
                <article class="public-step">
                    <span class="step-number">{{ $step->order }}</span>
                    <h2>{{ $step->title }}</h2>
                    <p>{{ $step->content }}</p>
                    @if ($step->image_url)
                        <img src="{{ $step->image_url }}" alt="{{ $step->title }}" loading="lazy">
                    @endif
                </article>
            @endforeach
        </section>

        @include('partials.site-footer')
    </main>
</body>
</html>
