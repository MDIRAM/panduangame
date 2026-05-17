<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $game['title'] }} | Walkthrough Game Hub</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet"/>
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="welcome-page" style="background: radial-gradient(circle at top left, #0a1222 0%, #101a36 35%, #070b15 100%); background-color: #070b15;">
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
                <div style="height:380px;background-image: url('{{ route('cover', ['slug' => $slug]) }}'); background-size: cover; background-position:center;"></div>
            </div>

            <p class="welcome-intro" style="margin-top:1rem;">{{ $game['description'] }}</p>

            <div class="game-detail-grid">
                <div class="game-detail-card">
                    <h3>Guide overview</h3>
                    <p>Halaman ini menyajikan panduan utama untuk judul tersebut, fokus pada story dan rute yang paling relevan untuk tamat dengan jelas.</p>
                </div>
                <div class="game-detail-card">
                    <h3>Fast track</h3>
                    <p>Kami berikan poin penting yang harus kamu kerjakan dulu, sehingga permainan bisa selesai lebih efisien tanpa melewatkan cerita utama.</p>
                </div>
            </div>

            <div class="game-detail-grid" style="margin-top: 1rem;">
                <div class="game-detail-card" style="grid-column: span 2;">
                    <h3>Highlight penting</h3>
                    <ul class="detail-list">
                        @foreach ($game['highlights'] as $highlight)
                            <li>{{ $highlight }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
