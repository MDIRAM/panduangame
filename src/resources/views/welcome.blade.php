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
        <div class="welcome-layout">
            <main class="welcome-card">
                <header class="welcome-header">
                    <div>
                        <p class="brand-label">Walkthrough Game Hub</p>
                        <h1>Finish your story/game with a focused walkthrough.</h1>
                    </div>
                    <div class="welcome-actions">
                        <a href="/login" class="button secondary">Log in</a>
                        <a href="/register" class="button primary">Register</a>
                    </div>
                </header>

                <p class="welcome-intro">Panduan multi-game untuk story, boss, dan misi penting.</p>

                <div class="hero-grid">
                    <article class="hero-card">
                        <span class="hero-tag">Feature</span>
                        <strong>Fast route</strong>
                        <p>Guide step-by-step untuk menyelesaikan cerita tanpa langkah yang sia-sia.</p>
                    </article>
                    <article class="hero-card hero-card-alt">
                        <span class="hero-tag">Focus</span>
                        <strong>Story first</strong>
                        <p>Sistem panduan jatuh ke story dan misi utama, bukan sekedar target trophy.</p>
                    </article>
                </div>

                <section class="intro-panel">
                    <h2>Why this hub works</h2>
                    <p>Halaman ini dirancang untuk pemain yang ingin tamat dengan cepat dan jelas. Kami berikan rute inti, boss strategy, dan ringkasan misi tanpa campur achievement 100%.</p>
                </section>

                <div class="feature-grid">
                    <article class="feature-card">
                        <h3>Clear route</h3>
                        <p>Tiap guide dipisah jadi bagian utama agar gampang diikuti.</p>
                    </article>
                    <article class="feature-card">
                        <h3>Quick overview</h3>
                        <p>Ringkas, langsung ke inti, dan mudah dipahami untuk semua level pemain.</p>
                    </article>
                    <article class="feature-card">
                        <h3>Updated often</h3>
                        <p>Konten dapat di-update kapan saja sesuai patch baru atau perubahan mekanik.</p>
                    </article>
                </div>

                <div class="mt-10">
                    <div class="mb-5">
                        <p class="text-xs uppercase tracking-[0.3em] text-[#8f8d88] dark:text-[#a3a29f]">Game tersedia</p>
                        <h3 class="mt-3 text-2xl font-semibold text-[#1b1b18] dark:text-[#f7f5f0]">6 judul siap panduan</h3>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                        <a href="{{ route('games.show', ['slug' => 'elden-ring']) }}" class="game-card-link">
                            <article class="rounded-[28px] overflow-hidden border border-[#e6e4de] dark:border-[#2b2b2b] bg-[#f7f5f2] dark:bg-[#191919] shadow-sm game-cover-card elden-cover">
                                <div class="h-40 bg-slate-300 dark:bg-slate-800 flex items-end p-4" style="background-image: url('{{ route('cover', ['slug' => 'elden-ring']) }}'); background-size: cover; background-position: center;">
                                    <span class="text-lg font-semibold text-[#1b1b18] dark:text-[#f6f5f0]">Elden Ring</span>
                                </div>
                                <div class="px-5 py-4">
                                    <p class="text-sm text-[#6d6b67] dark:text-[#b8b5ae]">Walkthrough story, boss, dan rute inti.</p>
                                </div>
                            </article>
                        </a>
                        <a href="{{ route('games.show', ['slug' => 'dark-souls-1']) }}" class="game-card-link">
                            <article class="rounded-[28px] overflow-hidden border border-[#e6e4de] dark:border-[#2b2b2b] bg-[#f7f5f2] dark:bg-[#191919] shadow-sm game-cover-card dark-souls-cover">
                                <div class="h-40 bg-slate-300 dark:bg-slate-800 flex items-end p-4">
                                    <span class="text-lg font-semibold text-[#1b1b18] dark:text-[#f6f5f0]">Dark Souls 1</span>
                                </div>
                                <div class="px-5 py-4">
                                    <p class="text-sm text-[#6d6b67] dark:text-[#b8b5ae]">Panduan story hingga Lord Souls.</p>
                                </div>
                            </article>
                        </a>
                        <a href="{{ route('games.show', ['slug' => 'dark-souls-2']) }}" class="game-card-link">
                            <article class="rounded-[28px] overflow-hidden border border-[#e6e4de] dark:border-[#2b2b2b] bg-[#f7f5f2] dark:bg-[#191919] shadow-sm game-cover-card dark-souls-cover">
                                <div class="h-40 bg-slate-300 dark:bg-slate-800 flex items-end p-4">
                                    <span class="text-lg font-semibold text-[#1b1b18] dark:text-[#f6f5f0]">Dark Souls 2</span>
                                </div>
                                <div class="px-5 py-4">
                                    <p class="text-sm text-[#6d6b67] dark:text-[#b8b5ae]">Rute clear story dengan boss utama.</p>
                                </div>
                            </article>
                        </a>
                        <a href="{{ route('games.show', ['slug' => 'dark-souls-3']) }}" class="game-card-link">
                            <article class="rounded-[28px] overflow-hidden border border-[#e6e4de] dark:border-[#2b2b2b] bg-[#f7f5f2] dark:bg-[#191919] shadow-sm game-cover-card dark-souls-cover">
                                <div class="h-40 bg-slate-300 dark:bg-slate-800 flex items-end p-4">
                                    <span class="text-lg font-semibold text-[#1b1b18] dark:text-[#f6f5f0]">Dark Souls 3</span>
                                </div>
                                <div class="px-5 py-4">
                                    <p class="text-sm text-[#6d6b67] dark:text-[#b8b5ae]">Panduan ending ke Ashes dan The Ringed City.</p>
                                </div>
                            </article>
                        </a>
                        <a href="{{ route('games.show', ['slug' => 'persona-3']) }}" class="game-card-link">
                            <article class="rounded-[28px] overflow-hidden border border-[#e6e4de] dark:border-[#2b2b2b] bg-[#f7f5f2] dark:bg-[#191919] shadow-sm game-cover-card persona3-cover">
                                <div class="game-cover-figure" style="background-image: url('{{ route('cover', ['slug' => 'persona-3']) }}'); background-size: cover; background-position: center;">
                                    <div class="game-cover-badge">Persona 3</div>
                                </div>
                                <div class="px-5 py-4">
                                    <p class="text-sm text-[#6d6b67] dark:text-[#b8b5ae]">Walkthrough cerita utama dan social link.</p>
                                </div>
                            </article>
                        </a>
                        <a href="{{ route('games.show', ['slug' => 'persona-4']) }}" class="game-card-link">
                            <article class="rounded-[28px] overflow-hidden border border-[#e6e4de] dark:border-[#2b2b2b] bg-[#f7f5f2] dark:bg-[#191919] shadow-sm game-cover-card persona4-cover">
                                <div class="h-40 bg-slate-300 dark:bg-slate-800 flex items-end p-4" style="background-image: url('{{ route('cover', ['slug' => 'persona-4']) }}'); background-size: cover; background-position: center;">
                                    <span class="text-lg font-semibold text-[#1b1b18] dark:text-[#f6f5f0]">Persona 4</span>
                                </div>
                                <div class="px-5 py-4">
                                    <p class="text-sm text-[#6d6b67] dark:text-[#b8b5ae]">Panduan tamat kasus misteri dan True Ending.</p>
                                </div>
                            </article>
                        </a>
                    </div>
                </div>
            </main>

            <aside class="welcome-aside">
                <div class="aside-block">
                    <p class="section-label">Popular reviews</p>
                    <ul class="review-list">
                        <li>
                            <strong>Horizon: Finish Main Story Fast</strong>
                            <span>Best route for story-driven players.</span>
                        </li>
                        <li>
                            <strong>The Last of Us Part II — Boss Guide</strong>
                            <span>Langkah jelas untuk pertempuran paling sulit.</span>
                        </li>
                        <li>
                            <strong>Elden Ring — Main Quest</strong>
                            <span>Rute optimal sampai ke ending tanpa side quest berat.</span>
                        </li>
                    </ul>
                </div>

                <div class="aside-block stats-block">
                    <div>
                        <span>6</span>
                        <p>Game walkthroughs</p>
                    </div>
                    <div>
                        <span>6</span>
                        <p>Available titles</p>
                    </div>
                </div>

                <div class="aside-block note-block">
                    <p class="note-title">Note</p>
                    <p>Halaman ini sudah siap jadi tampilan utama. Nanti bisa dihubungkan ke login, register, dan dashboard sesuai kebutuhan.</p>
                </div>
            </aside>
        </div>
    </div>
</body>
</html>
