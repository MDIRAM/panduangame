<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | Walkthrough Game Hub</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body class="auth-page">
    <div class="dashboard-shell auth-shell">
        <section class="dashboard-panel">
            <span class="hero-chip">Dashboard</span>
            <h1>Welcome back, {{ auth()->user()->name }}. Your next walkthrough starts here.</h1>
            <p>Ringkasan ini membaca langsung katalog dan walkthrough yang tersedia di database.</p>
            <div class="dashboard-stats">
                <div class="dashboard-stat">
                    <strong>{{ $gameCount }}</strong>
                    <h3>Published games</h3>
                    <p>Game yang saat ini tersedia di katalog publik.</p>
                </div>
                <div class="dashboard-stat">
                    <strong>{{ $chapterCount }}</strong>
                    <h3>Walkthrough chapters</h3>
                    <p>Chapter yang dapat dibuka dari seluruh game.</p>
                </div>
                <div class="dashboard-stat">
                    <strong>{{ $contributionCount }}</strong>
                    <h3>Kontribusi saya</h3>
                    <p>{{ $pendingContributionCount }} sedang menunggu review admin.</p>
                </div>
                <div class="dashboard-stat">
                    <strong>{{ $stepCount }}</strong>
                    <h3>Guide steps</h3>
                    <p>Instruksi walkthrough yang tersimpan di database.</p>
                </div>
            </div>
            <div class="hero-actions">
                <a href="{{ route('contributions.index') }}" class="hero-button primary">Kelola Walkthrough</a>
                <a href="{{ route('contributions.create') }}" class="hero-button secondary">Buat Kontribusi</a>
            </div>
        </section>

        <aside class="dashboard-card">
            <div class="dashboard-summary">
                <h2>Latest walkthrough highlights</h2>
                <p>Chapter yang paling baru diperbarui oleh pengelola konten.</p>
            </div>
            <div class="dashboard-list">
                @forelse ($latestChapters as $chapter)
                    <article class="dashboard-item">
                        <h4>{{ $chapter->game->title }} - {{ $chapter->chapter_title }}</h4>
                        <p>{{ $chapter->steps_count }} langkah. Diperbarui {{ $chapter->updated_at->diffForHumans() }}.</p>
                    </article>
                @empty
                    <article class="dashboard-item">
                        <h4>Belum ada walkthrough</h4>
                        <p>Konten akan muncul setelah chapter pertama dibuat.</p>
                    </article>
                @endforelse
            </div>
            <a href="/" class="dashboard-link">
                Continue browsing guides
            </a>
            <form action="{{ route('logout') }}" method="POST" class="logout-form">
                @csrf
                <button type="submit">Log out</button>
            </form>
        </aside>
    </div>
</body>
</html>
