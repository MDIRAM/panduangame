<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $missionTitle }} | Persona 3 Guide</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet"/>
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
    <div class="min-h-screen bg-[radial-gradient(circle_at_top_left,#172554_0%,#020617_42%,#020617_100%)]">
        <main class="mx-auto w-full max-w-5xl px-5 py-8 lg:px-8">
            <a href="{{ route('games.show', ['slug' => 'persona-3']) }}" class="inline-flex rounded bg-slate-800 px-4 py-2 text-sm font-bold text-white transition hover:bg-red-600">Back to Story Mission Walkthroughs</a>

            <header class="mt-8 border-b border-slate-800 pb-6">
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-red-500">Persona 3 Walkthrough</p>
                <h1 class="mt-3 text-4xl font-black tracking-tight text-white">{{ $missionTitle }}</h1>
            </header>

            @if ($mission === 'prologue-april-7-april-18')
                <article class="mt-8 space-y-6">
                    @php
                        $prologueSections = json_decode(file_get_contents(resource_path('data/persona3/prologue.json')), true);
                    @endphp

                    @foreach ($prologueSections as $section)
                        @if ($section['type'] === 'heading')
                            <h2 class="border-b border-slate-800 pb-3 text-2xl font-extrabold text-white">{{ $section['body'] }}</h2>
                        @elseif ($section['type'] === 'paragraph')
                            <p class="text-lg leading-8 text-slate-200">{!! $section['body'] !!}</p>
                        @elseif ($section['type'] === 'image')
                            @php
                                $imagePath = 'coverimg/Persona3/'.$section['image'];
                            @endphp

                            <figure>
                                @if (file_exists(public_path($imagePath)))
                                    <img src="{{ asset($imagePath) }}" alt="{{ $section['alt'] }}" class="w-full max-w-2xl rounded my-4">
                                @else
                                    <div class="my-4 grid min-h-56 w-full max-w-2xl place-items-center rounded border border-slate-800 bg-slate-900 text-slate-400">
                                        <span>{{ $section['label'] }}</span>
                                    </div>
                                @endif
                            </figure>
                        @endif
                    @endforeach

                    <nav class="border-t border-slate-800 pt-6" aria-label="Story mission navigation">
                        <h2 class="text-xl font-extrabold text-white">Up Next: First Visit to Tartarus (April 19 - April 20) Walkthrough</h2>

                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                            <a href="{{ route('games.show', ['slug' => 'persona-3']) }}" class="rounded border border-slate-800 bg-slate-900 p-4 transition hover:border-red-500">
                                <span>Previous</span>
                                <strong class="block text-white">Story Mission Walkthroughs</strong>
                            </a>

                            <a href="{{ route('persona.story.show', ['mission' => 'first-visit-to-tartarus-april-19-april-20']) }}" class="rounded border border-slate-800 bg-slate-900 p-4 transition hover:border-red-500">
                                <span>Next</span>
                                <strong class="block text-white">First Visit to Tartarus (April 19 - April 20) Walkthrough</strong>
                            </a>
                        </div>
                    </nav>
                </article>
            @else
                <section class="mt-8 rounded border border-slate-800 bg-slate-900 p-6">
                    <h2 class="text-2xl font-extrabold text-white">Guide content coming soon</h2>
                    <p class="mt-3 text-lg leading-8 text-slate-200">Halaman ini sudah disiapkan untuk detail walkthrough misi. Nanti bagian ini bisa diisi step cerita, pilihan penting, boss, Tartarus floor, dan item yang wajib diambil.</p>
                </section>
            @endif
        </main>
    </div>
</body>
</html>
