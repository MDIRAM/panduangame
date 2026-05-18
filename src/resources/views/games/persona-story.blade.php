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
<body class="welcome-page persona-story-body">
    <div class="welcome-shell">
        <main class="persona-story-detail">
            <a href="{{ route('games.show', ['slug' => 'persona-3']) }}" class="persona-back-link">Back to Story Mission Walkthroughs</a>

            <header>
                <p>Persona 3 Walkthrough</p>
                <h1>{{ $missionTitle }}</h1>
            </header>

            @if ($mission === 'prologue-april-7-april-18')
                <article class="persona-guide-article">
                    @php
                        $prologueSections = json_decode(file_get_contents(resource_path('data/persona3/prologue.json')), true);
                    @endphp

                    @foreach ($prologueSections as $section)
                        @if ($section['type'] === 'heading')
                            <h2 class="persona-article-heading">{{ $section['body'] }}</h2>
                        @elseif ($section['type'] === 'paragraph')
                            <p>{!! $section['body'] !!}</p>
                        @elseif ($section['type'] === 'image')
                            @php
                                $imagePath = 'coverimg/Persona3/'.$section['image'];
                            @endphp

                            <figure class="persona-walkthrough-image">
                                @if (file_exists(public_path($imagePath)))
                                    <img src="{{ asset($imagePath) }}" alt="{{ $section['alt'] }}">
                                @else
                                    <div class="persona-image-placeholder">
                                        <span>{{ $section['label'] }}</span>
                                    </div>
                                @endif
                            </figure>
                        @endif
                    @endforeach

                    <nav class="persona-story-pager" aria-label="Story mission navigation">
                        <h2>Up Next: First Visit to Tartarus (April 19 - April 20) Walkthrough</h2>

                        <div>
                            <a href="{{ route('games.show', ['slug' => 'persona-3']) }}" class="persona-pager-card">
                                <span>Previous</span>
                                <strong>Story Mission Walkthroughs</strong>
                            </a>

                            <a href="{{ route('persona.story.show', ['mission' => 'first-visit-to-tartarus-april-19-april-20']) }}" class="persona-pager-card next">
                                <span>Next</span>
                                <strong>First Visit to Tartarus (April 19 - April 20) Walkthrough</strong>
                            </a>
                        </div>
                    </nav>
                </article>
            @else
                <section>
                    <h2>Guide content coming soon</h2>
                    <p>Halaman ini sudah disiapkan untuk detail walkthrough misi. Nanti bagian ini bisa diisi step cerita, pilihan penting, boss, Tartarus floor, dan item yang wajib diambil.</p>
                </section>
            @endif
        </main>
    </div>
</body>
</html>
