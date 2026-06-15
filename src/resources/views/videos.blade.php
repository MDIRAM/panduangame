<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#070b15">
    <title>Video Walkthrough | Walkthrough Game Hub</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet"/>
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}?v={{ filemtime(public_path('css/welcome.css')) }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="welcome-page" style="background: radial-gradient(circle at top left, #0a1222 0%, #101a36 35%, #070b15 100%); background-color: #070b15;">
    <div class="welcome-shell">
        <div class="guide-layout">
            <aside class="guide-sidebar">
                <a href="/" class="guide-logo" aria-label="PanduanGame home">
                    <img src="{{ asset('coverimg/logogamepanduan.png') }}" alt="PanduanGame logo">
                </a>

                <nav class="guide-nav" aria-label="Main navigation">
                    <a href="/">Home</a>
                    <a href="/#guides">Guides</a>
                    <a href="{{ route('videos.index') }}" class="active">Videos</a>
                    @auth
                        @if (auth()->user()->hasRole('super_admin'))
                            <a href="/admin">Admin Panel</a>
                        @else
                            @if (auth()->user()->hasRole('contributor'))
                                <a href="{{ route('contributions.index') }}">Contributor Dashboard</a>
                            @endif
                            <a href="{{ route('dashboard') }}">My Account</a>
                        @endif
                        <form action="{{ route('logout') }}" method="POST" class="guide-nav-logout">
                            @csrf
                            <button type="submit">Log out</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}">Login</a>
                    @endauth
                </nav>
            </aside>

            <main class="guide-main">
                <header class="section-heading video-page-heading">
                    <div>
                        <h2>Video game walkthroughs</h2>
                    </div>
                    <span>3 available videos</span>
                </header>

                <section class="video-guide-grid" aria-label="Video walkthrough list">
                    <a href="https://www.youtube.com/results?search_query=Elden+Ring+main+quest+walkthrough" class="video-guide-card" target="_blank" rel="noopener">
                        <img src="{{ asset('coverimg/EldenRing.png') }}" alt="Elden Ring walkthrough thumbnail">
                        <strong>Elden Ring Video Walkthrough</strong>
                        <span>Main Quest Walkthrough</span>
                    </a>

                    <a href="https://www.youtube.com/results?search_query=Persona+3+social+link+schedule+walkthrough" class="video-guide-card" target="_blank" rel="noopener">
                        <img src="{{ asset('coverimg/Persona_3.webp') }}" alt="Persona 3 walkthrough thumbnail">
                        <strong>Persona 3 Video Walkthrough</strong>
                        <span>Social Link Schedule</span>
                    </a>

                    <a href="https://youtu.be/N8B_rMgQts8?si=7mR28IqZi2Xba5ye" class="video-guide-card" target="_blank" rel="noopener">
                        <img src="{{ asset('coverimg/Dark_Souls_2.jpg') }}" alt="Dark Souls 2 walkthrough thumbnail">
                        <strong>Dark Souls 2 Video Walkthrough</strong>
                        <span>Drangleic Story Route</span>
                    </a>
                </section>

                @include('partials.site-footer')
            </main>
        </div>
    </div>
</body>
</html>
