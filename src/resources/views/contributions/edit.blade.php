<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#080d18">
    <title>{{ $contribution->title }} | Contributor Dashboard</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/contributions.css') }}?v={{ filemtime(public_path('css/contributions.css')) }}">
</head>
<body class="contribution-page">
    @php($editable = $contribution->isEditableByAuthor())
    <main class="contribution-shell">
        <nav class="contribution-topbar">
            <a href="{{ route('contributions.index') }}" class="button">My Walkthroughs</a>
            <span class="status {{ $contribution->status }}">
                {{ \App\Models\WalkthroughContribution::statuses()[$contribution->status] }}
            </span>
        </nav>

        <header class="contribution-heading">
            <div>
                <p class="eyebrow">{{ $contribution->game->title }}</p>
                <h1>{{ $contribution->title }}</h1>
            </div>
        </header>

        @if (session('success'))
            <div class="notice">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="notice error">
                {{ $errors->first() }}
            </div>
        @endif

        @if ($contribution->moderation_notes)
            <p class="moderation-note">
                <strong>Catatan admin:</strong> {{ $contribution->moderation_notes }}
            </p>
        @endif

        <div class="form-layout">
            <section class="form-panel">
                <h2>Informasi Walkthrough</h2>
                <form method="POST" action="{{ route('contributions.update', $contribution) }}" class="form-grid">
                    @csrf
                    @method('PUT')

                    <div class="field">
                        <label for="game_id">Game</label>
                        <select id="game_id" name="game_id" required @disabled(! $editable)>
                            @foreach ($games as $game)
                                <option value="{{ $game->id }}" @selected(old('game_id', $contribution->game_id) == $game->id)>
                                    {{ $game->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field">
                        <label for="chapter_id">Chapter / misi</label>
                        <select id="chapter_id" name="chapter_id" required @disabled(! $editable)>
                            @foreach ($chapters->groupBy(fn ($chapter) => $chapter->game->title) as $gameTitle => $gameChapters)
                                <optgroup label="{{ $gameTitle }}">
                                    @foreach ($gameChapters as $chapter)
                                        <option
                                            value="{{ $chapter->id }}"
                                            data-game-id="{{ $chapter->game_id }}"
                                            @selected(old('chapter_id', $contribution->chapter_id) == $chapter->id)
                                        >
                                            {{ $chapter->parent_id ? '— ' : '' }}{{ $chapter->chapter_title }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <div class="field">
                        <label for="title">Judul walkthrough</label>
                        <input
                            id="title"
                            name="title"
                            value="{{ old('title', $contribution->title) }}"
                            maxlength="150"
                            required
                            @disabled(! $editable)
                        >
                    </div>

                    <div class="field">
                        <label for="summary">Ringkasan</label>
                        <textarea id="summary" name="summary" maxlength="1500" required @disabled(! $editable)>{{ old('summary', $contribution->summary) }}</textarea>
                    </div>

                    @if ($editable)
                        <button type="submit" class="button primary">Simpan Perubahan</button>
                    @endif
                </form>
            </section>

            <aside class="form-panel">
                <h2>Review</h2>
                <p class="muted">{{ $contribution->steps->count() }} langkah tersimpan.</p>

                @if ($editable)
                    <form method="POST" action="{{ route('contributions.submit', $contribution) }}">
                        @csrf
                        <button type="submit" class="button primary">Kirim ke Admin</button>
                    </form>

                    <form method="POST" action="{{ route('contributions.destroy', $contribution) }}" style="margin-top:12px;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="button danger">Hapus Draft</button>
                    </form>
                @elseif ($contribution->status === \App\Models\WalkthroughContribution::STATUS_PUBLISHED)
                    <a href="{{ route('contributions.show', $contribution) }}" class="button primary">Buka Halaman Publik</a>
                @else
                    <p class="muted">Walkthrough sedang menunggu keputusan admin.</p>
                @endif
            </aside>
        </div>

        <header class="contribution-heading">
            <div>
                <p class="eyebrow">Guide content</p>
                <h1>Langkah Walkthrough</h1>
            </div>
        </header>

        <section class="steps-list">
            @forelse ($contribution->steps as $step)
                <article class="step-card">
                    <span class="step-number">{{ $step->order }}</span>
                    <div>
                        <h2>{{ $step->title }}</h2>
                        <div class="rich-content">{!! $step->content !!}</div>
                        @if ($step->image_url)
                            <img src="{{ $step->image_url }}" alt="{{ $step->title }}" loading="lazy">
                        @endif
                    </div>
                    @if ($editable)
                        <div class="step-actions">
                            <a href="{{ route('contribution-steps.edit', $step) }}" class="button">Edit</a>
                            <form method="POST" action="{{ route('contribution-steps.destroy', $step) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="button danger">Hapus</button>
                            </form>
                        </div>
                    @endif
                </article>
            @empty
                <div class="empty-state">Belum ada langkah walkthrough.</div>
            @endforelse
        </section>

        @if ($editable)
            <section class="form-panel" style="margin-top:20px;">
                <h2>Tambah Langkah</h2>
                <form
                    method="POST"
                    action="{{ route('contribution-steps.store', $contribution) }}"
                    enctype="multipart/form-data"
                    class="form-grid two-columns"
                >
                    @csrf
                    <div class="field">
                        <label for="step_title">Judul langkah</label>
                        <input id="step_title" name="title" value="{{ old('title') }}" maxlength="150" required>
                    </div>
                    <div class="field">
                        <label for="order">Urutan</label>
                        <input id="order" name="order" type="number" min="1" max="999" value="{{ old('order', $contribution->steps->max('order') + 1) }}" required>
                    </div>
                    <div class="field full">
                        <label for="content">Isi panduan</label>
                        @include('contributions.partials.rich-editor', [
                            'id' => 'content',
                            'name' => 'content',
                            'value' => old('content'),
                        ])
                    </div>
                    <div class="field full">
                        <label for="image">Gambar pendukung</label>
                        <input id="image" name="image" type="file" accept=".jpg,.jpeg,.png,.webp">
                    </div>
                    <div class="field full">
                        <button type="submit" class="button primary">Tambah Langkah</button>
                    </div>
                </form>
            </section>
        @endif

        @include('partials.site-footer')
    </main>
    <script>
        const gameSelect = document.querySelector('#game_id');
        const chapterSelect = document.querySelector('#chapter_id');

        function syncChapterOptions() {
            const gameId = gameSelect.value;
            const selectedOption = chapterSelect.selectedOptions[0];

            chapterSelect.querySelectorAll('option[data-game-id]').forEach((option) => {
                const matchesGame = !gameId || option.dataset.gameId === gameId;
                option.disabled = !matchesGame;
                option.hidden = !matchesGame;
            });

            if (selectedOption && selectedOption.disabled) {
                chapterSelect.value = '';
            }
        }

        gameSelect?.addEventListener('change', syncChapterOptions);
        syncChapterOptions();
    </script>
    @include('contributions.partials.rich-editor-script')
</body>
</html>
