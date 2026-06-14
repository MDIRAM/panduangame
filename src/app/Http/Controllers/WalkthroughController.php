<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Game;
use Illuminate\View\View;

class WalkthroughController extends Controller
{
    public function showGame(): View
    {
        $game = Game::with([
            'chapters' => fn ($query) => $query->orderBy('order'),
            'chapters.steps' => fn ($query) => $query->orderBy('order'),
        ])
            ->where('slug', 'persona-3-reload')
            ->firstOrFail();

        return view('walkthrough.game_show', compact('game'));
    }

    public function showChapter(string $slug): View
    {
        return $this->chapterView($slug);
    }

    public function showMission(string $mission): View
    {
        return $this->chapterView($mission, 'persona-3-reload');
    }

    public function showGameChapter(string $gameSlug, string $chapterSlug): View
    {
        return $this->chapterView($chapterSlug, $gameSlug);
    }

    private function chapterView(string $slug, ?string $gameSlug = null): View
    {
        $chapter = Chapter::with([
            'game',
            'steps' => fn ($query) => $query->orderBy('order', 'asc'),
        ])
            ->where('slug', $slug)
            ->when(
                $gameSlug,
                fn ($query) => $query->whereHas(
                    'game',
                    fn ($gameQuery) => $gameQuery->where('slug', $gameSlug),
                ),
            )
            ->firstOrFail();

        $previousChapter = Chapter::query()
            ->where('game_id', $chapter->game_id)
            ->where('order', '<', $chapter->order)
            ->orderByDesc('order')
            ->first();

        $nextChapter = Chapter::query()
            ->where('game_id', $chapter->game_id)
            ->where('order', '>', $chapter->order)
            ->orderBy('order')
            ->first();

        return view('walkthrough.chapter_show', compact(
            'chapter',
            'previousChapter',
            'nextChapter',
        ));
    }
}
