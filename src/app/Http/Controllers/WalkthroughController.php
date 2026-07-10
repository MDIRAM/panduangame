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
        return $this->chapterView($slug, 'persona-3-reload');
    }

    public function showMission(string $mission): View
    {
        return $this->chapterView($mission, 'persona-3-reload');
    }

    public function showGameChapter(string $gameSlug, string $chapterSlug): View
    {
        $game = Game::where('route_slug', $gameSlug)
            ->where('is_published', true)
            ->firstOrFail();

        return $this->chapterView($chapterSlug, $game->slug);
    }

    private function chapterView(string $slug, ?string $gameSlug = null): View
    {
        $chapter = Chapter::with([
            'game',
            'parent',
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

        $gameChapters = Chapter::query()
            ->where('game_id', $chapter->game_id)
            ->orderBy('order')
            ->get([
                'id',
                'game_id',
                'parent_id',
                'chapter_title',
                'section_title',
                'slug',
                'order',
            ]);

        $chapter->game->loadCount('ratings')->loadAvg('ratings', 'rating');

        $isFavorited = auth()->check()
            && auth()->user()->gameFavorites()->where('game_id', $chapter->game_id)->exists();
        $userRating = auth()->check()
            ? auth()->user()->gameRatings()->where('game_id', $chapter->game_id)->value('rating')
            : null;

        return view('walkthrough.chapter_show', compact(
            'chapter',
            'gameChapters',
            'isFavorited',
            'previousChapter',
            'nextChapter',
            'userRating',
        ));
    }
}
