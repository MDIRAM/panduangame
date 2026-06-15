<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(): View
    {
        $games = Game::query()
            ->where('is_published', true)
            ->withCount('chapters')
            ->orderByDesc('is_featured')
            ->orderBy('title')
            ->get();

        return view('welcome', [
            'games' => $games,
            'featuredGame' => $games->firstWhere('is_featured', true) ?? $games->first(),
        ]);
    }

    public function show(string $slug): RedirectResponse|View
    {
        $game = Game::query()
            ->where('route_slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        if ($firstChapter = $game->chapters()->first()) {
            $route = $game->slug === 'persona-3-reload'
                ? route('persona.story.show', ['mission' => $firstChapter->slug])
                : route('games.walkthrough.show', [
                    'gameSlug' => $game->route_slug,
                    'chapterSlug' => $firstChapter->slug,
                ]);

            return redirect()->to($route);
        }

        $game->load([
            'walkthroughContributions' => fn ($query) => $query
                ->published()
                ->with('author')
                ->withCount('steps')
                ->latest('published_at'),
        ]);

        return view('games.show', compact('game'));
    }
}
