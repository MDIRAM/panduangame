<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#080d18">
    <title>Buat Walkthrough | Walkthrough Game Hub</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/contributions.css') }}?v={{ filemtime(public_path('css/contributions.css')) }}">
</head>
<body class="contribution-page">
    <main class="contribution-shell">
        <nav class="contribution-topbar">
            <a href="{{ route('contributions.index') }}" class="button">Kembali</a>
        </nav>

        <header class="contribution-heading">
            <div>
                <p class="eyebrow">New contribution</p>
                <h1>Buat Walkthrough</h1>
            </div>
        </header>

        <section class="form-panel">
            <form method="POST" action="{{ route('contributions.store') }}" class="form-grid two-columns">
                @csrf
                <div class="field">
                    <label for="game_id">Game</label>
                    <select id="game_id" name="game_id" required>
                        <option value="">Pilih game</option>
                        @foreach ($games as $game)
                            <option value="{{ $game->id }}" @selected(old('game_id') == $game->id)>
                                {{ $game->title }}
                            </option>
                        @endforeach
                    </select>
                    @error('game_id') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="field">
                    <label for="title">Judul walkthrough</label>
                    <input id="title" name="title" value="{{ old('title') }}" maxlength="150" required>
                    @error('title') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="field full">
                    <label for="summary">Ringkasan</label>
                    <textarea id="summary" name="summary" maxlength="1500" required>{{ old('summary') }}</textarea>
                    @error('summary') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="field full">
                    <button type="submit" class="button primary">Simpan Draft</button>
                </div>
            </form>
        </section>

        @include('partials.site-footer')
    </main>
</body>
</html>
