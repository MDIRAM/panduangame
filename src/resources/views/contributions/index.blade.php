<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Walkthrough Saya | Walkthrough Game Hub</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/contributions.css') }}">
</head>
<body class="contribution-page">
    <main class="contribution-shell">
        <nav class="contribution-topbar">
            <a href="{{ route('dashboard') }}" class="button">Dashboard</a>
            <a href="{{ route('contributions.create') }}" class="button primary">Buat Walkthrough</a>
        </nav>

        <header class="contribution-heading">
            <div>
                <p class="eyebrow">Community contributor</p>
                <h1>Walkthrough Saya</h1>
            </div>
            <p class="muted">{{ $contributions->count() }} kontribusi</p>
        </header>

        @if (session('success'))
            <div class="notice">{{ session('success') }}</div>
        @endif

        <section class="contribution-grid">
            @forelse ($contributions as $contribution)
                <article class="contribution-card">
                    <div class="status-row">
                        <span class="status {{ $contribution->status }}">
                            {{ \App\Models\WalkthroughContribution::statuses()[$contribution->status] }}
                        </span>
                        <span class="contribution-meta">{{ $contribution->steps_count }} langkah</span>
                    </div>
                    <h2>{{ $contribution->title }}</h2>
                    <p class="contribution-meta">{{ $contribution->game->title }}</p>
                    <p>{{ Str::limit($contribution->summary, 150) }}</p>
                    <div class="contribution-actions">
                        <a href="{{ route('contributions.edit', $contribution) }}" class="button">
                            {{ $contribution->isEditableByAuthor() ? 'Kelola' : 'Lihat Status' }}
                        </a>
                        @if ($contribution->status === \App\Models\WalkthroughContribution::STATUS_PUBLISHED)
                            <a href="{{ route('contributions.show', $contribution) }}" class="button">Buka Publik</a>
                        @endif
                    </div>
                </article>
            @empty
                <div class="empty-state">
                    Belum ada walkthrough komunitas dari akun ini.
                </div>
            @endforelse
        </section>
    </main>
</body>
</html>
