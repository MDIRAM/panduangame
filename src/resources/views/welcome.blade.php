<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Walkthrough Game Hub | Sistem Panduan</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet"/>
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
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
                    <a href="/" class="active">Home</a>
                    <a href="#guides">Guides</a>
                    <a href="{{ route('videos.index') }}">Videos</a>
                    <a href="/login">Login</a>
                    <a href="/dashboard">Dashboard</a>
                </nav>
            </aside>

            <main class="guide-main">
                <header class="guide-hero">
                    <div>
                        <p class="brand-label">Walkthrough Game Hub</p>
                        <h1>Find the fastest route through your next game.</h1>
                        <p class="welcome-intro">Cari rute story, strategi boss, dan panduan tamat untuk game favoritmu tanpa harus buka banyak tab.</p>
                    </div>
                    <div class="welcome-actions">
                        <a href="#guides" class="button primary">Browse guides</a>
                        <a href="/login" class="button secondary">Log in</a>
                    </div>
                </header>

                <section class="spotlight-strip" aria-label="Featured guide" data-spotlight style="--spotlight-image: url('{{ asset('coverimg/EldenRing.png') }}');">
                    <div>
                        <span class="hero-tag" data-spotlight-label>Featured Route</span>
                        <h2 data-spotlight-title>Elden Ring: Road to Endgame</h2>
                        <p data-spotlight-copy>Rute utama menuju final boss, item penting, dan checkpoint yang wajib kamu ambil dulu.</p>
                    </div>
                    <a href="{{ route('games.show', ['slug' => 'elden-ring']) }}" data-spotlight-link>Open guide</a>
                </section>

                <section class="guide-section" id="guides">
                    <div class="section-heading">
                        <div>
                            <p class="section-label">Guides library</p>
                            <h2>Popular game walkthroughs</h2>
                        </div>
                        <span>3 available titles</span>
                    </div>

                    <div class="game-guide-grid">
                        <a href="{{ route('games.show', ['slug' => 'elden-ring']) }}" class="guide-game-card">
                            <img src="{{ asset('coverimg/EldenRing.png') }}" alt="Elden Ring guide cover">
                            <strong>Elden Ring Guide</strong>
                            <span>Main Quest Walkthrough</span>
                        </a>
                        <a href="{{ route('games.show', ['slug' => 'dark-souls-2']) }}" class="guide-game-card">
                            <img src="{{ asset('coverimg/Dark_Souls_2.jpg') }}" alt="Dark Souls 2 guide cover">
                            <strong>Dark Souls 2 Guide</strong>
                            <span>Drangleic Story Route</span>
                        </a>
                        <a href="{{ route('games.show', ['slug' => 'persona-3']) }}" class="guide-game-card">
                            <img src="{{ route('cover', ['slug' => 'persona-3']) }}" alt="Persona 3 guide cover">
                            <strong>Persona 3 Guide</strong>
                            <span>Social Link Schedule</span>
                        </a>
                    </div>
                </section>

            </main>
        </div>
    </div>
    <script>
        const spotlight = document.querySelector('[data-spotlight]');

        if (spotlight) {
            const title = spotlight.querySelector('[data-spotlight-title]');
            const copy = spotlight.querySelector('[data-spotlight-copy]');
            const link = spotlight.querySelector('[data-spotlight-link]');
            const label = spotlight.querySelector('[data-spotlight-label]');
            const slides = [
                {
                    label: 'Featured Route',
                    title: 'Elden Ring: Road to Endgame',
                    copy: 'Rute utama menuju final boss, item penting, dan checkpoint yang wajib kamu ambil dulu.',
                    href: "{{ route('games.show', ['slug' => 'elden-ring']) }}",
                    image: "{{ asset('coverimg/EldenRing.png') }}",
                },
                {
                    label: 'Social Link Route',
                    title: 'Persona 3: Tartarus & Social Link',
                    copy: 'Atur jadwal harian, prioritas Social Link, dan progres Tartarus supaya playthrough lebih rapi.',
                    href: "{{ route('games.show', ['slug' => 'persona-3']) }}",
                    image: "{{ asset('coverimg/Persona_3.webp') }}",
                },
                {
                    label: 'Drangleic Route',
                    title: 'Dark Souls 2: Story Progress',
                    copy: 'Ikuti urutan area, boss utama, dan shortcut penting untuk menembus Drangleic sampai endgame.',
                    href: "{{ route('games.show', ['slug' => 'dark-souls-2']) }}",
                    image: "{{ asset('coverimg/Dark_Souls_2.jpg') }}",
                },
            ];
            let activeSlide = 0;

            window.setInterval(() => {
                activeSlide = (activeSlide + 1) % slides.length;
                const slide = slides[activeSlide];

                spotlight.classList.add('is-changing');

                window.setTimeout(() => {
                    label.textContent = slide.label;
                    title.textContent = slide.title;
                    copy.textContent = slide.copy;
                    link.href = slide.href;
                    spotlight.style.setProperty('--spotlight-image', `url('${slide.image}')`);
                    spotlight.classList.remove('is-changing');
                }, 420);
            }, 4200);
        }
    </script>
</body>
</html>
