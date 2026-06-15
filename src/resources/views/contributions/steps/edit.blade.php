<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Langkah | {{ $step->contribution->title }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/contributions.css') }}">
</head>
<body class="contribution-page">
    <main class="contribution-shell">
        <nav class="contribution-topbar">
            <a href="{{ route('contributions.edit', $step->contribution) }}" class="button">Kembali</a>
        </nav>

        <header class="contribution-heading">
            <div>
                <p class="eyebrow">{{ $step->contribution->game->title }}</p>
                <h1>Edit Langkah</h1>
            </div>
        </header>

        <section class="form-panel">
            <form
                method="POST"
                action="{{ route('contribution-steps.update', $step) }}"
                enctype="multipart/form-data"
                class="form-grid two-columns"
            >
                @csrf
                @method('PUT')
                <div class="field">
                    <label for="title">Judul langkah</label>
                    <input id="title" name="title" value="{{ old('title', $step->title) }}" maxlength="150" required>
                </div>
                <div class="field">
                    <label for="order">Urutan</label>
                    <input id="order" name="order" type="number" min="1" max="999" value="{{ old('order', $step->order) }}" required>
                </div>
                <div class="field full">
                    <label for="content">Isi panduan</label>
                    <textarea id="content" name="content" maxlength="5000" required>{{ old('content', $step->content) }}</textarea>
                </div>
                <div class="field full">
                    <label for="image">Ganti gambar</label>
                    <input id="image" name="image" type="file" accept=".jpg,.jpeg,.png,.webp">
                </div>
                <div class="field full">
                    <button type="submit" class="button primary">Simpan Langkah</button>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
