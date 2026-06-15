<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#101318">
    <title>{{ $game->title }} | Game Walkthrough</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        html {
            min-height: 100%;
            background-color: #101318;
            color-scheme: dark;
        }

        .site-footer {
            display: flex;
            grid-column: 1 / -1;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            margin-top: 1rem;
            padding: 1.4rem 0 0.2rem;
            border-top: 1px solid rgba(148, 163, 184, 0.24);
            color: rgb(148 163 184);
        }

        .site-footer div {
            display: grid;
            gap: 0.25rem;
        }

        .site-footer strong {
            color: #ffffff;
        }

        .site-footer nav {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .site-footer a {
            color: rgb(203 213 225);
            text-decoration: none;
        }

        .site-footer a:hover {
            color: #ffffff;
        }

        @media (max-width: 640px) {
            .site-footer {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
</head>
<body class="min-h-screen bg-[#101318] text-slate-100">
    <main class="mx-auto grid min-h-screen w-full max-w-6xl gap-8 px-5 py-8 lg:grid-cols-[320px_1fr] lg:px-8">
        <aside class="lg:sticky lg:top-8 lg:h-fit">
            <div class="overflow-hidden border border-slate-800 bg-[#171b22] shadow-2xl shadow-black/30">
                @php
                    $coverImage = $game->cover_image && str_starts_with($game->cover_image, 'http')
                        ? $game->cover_image
                        : asset($game->cover_image ?? 'coverimg/Persona_3.webp');
                @endphp

                <img src="{{ $coverImage }}" alt="{{ $game->title }} cover" class="h-[430px] w-full object-cover">

                <div class="space-y-3 p-5">
                    <p class="text-xs font-bold uppercase tracking-[0.28em] text-red-500">IGN Style Guide</p>
                    <h1 class="text-3xl font-black leading-tight">{{ $game->title }}</h1>
                    <p class="text-sm leading-6 text-slate-300">{{ $game->description }}</p>
                </div>
            </div>
        </aside>

        <section class="space-y-6">
            <header class="border-b border-slate-800 pb-5">
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-red-500">Walkthrough Database</p>
                <h2 class="mt-3 text-4xl font-black tracking-tight text-white">Daftar Chapter</h2>
                <p class="mt-3 max-w-2xl text-slate-300">Semua bulan dan langkah panduan di halaman ini berasal dari tabel database, bukan HTML hardcoded.</p>
            </header>

            <div class="grid gap-4">
                @forelse ($game->chapters as $chapter)
                    <article class="border border-slate-800 bg-[#171b22] p-5 transition hover:border-red-500/70">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <span class="text-xs font-bold uppercase tracking-[0.22em] text-slate-500">Chapter {{ $chapter->order }}</span>
                                <h3 class="mt-2 text-2xl font-extrabold text-white">{{ $chapter->chapter_title }}</h3>
                                <p class="mt-2 text-sm text-slate-400">{{ $chapter->steps->count() }} langkah walkthrough tersedia.</p>
                            </div>

                            <a href="{{ route('walkthrough.chapter', $chapter->slug) }}" class="inline-flex items-center justify-center bg-red-600 px-4 py-2 text-sm font-bold text-white transition hover:bg-red-500">
                                Buka Chapter
                            </a>
                        </div>

                        <ul class="mt-5 grid gap-2 border-t border-slate-800 pt-4">
                            @foreach ($chapter->steps as $step)
                                <li class="flex gap-3 text-sm text-slate-300">
                                    <span class="font-bold text-red-500">#{{ $step->order }}</span>
                                    <span>{{ $step->step_title }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </article>
                @empty
                    <div class="border border-slate-800 bg-[#171b22] p-6 text-slate-300">
                        Belum ada chapter. Jalankan migration dan seeder terlebih dahulu.
                    </div>
                @endforelse
            </div>
        </section>

        @include('partials.site-footer')
    </main>
</body>
</html>
