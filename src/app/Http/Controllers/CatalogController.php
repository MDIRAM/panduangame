<?php

namespace App\Http\Controllers;

use App\Models\Game;
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

    public function show(string $slug): View
    {
        $game = Game::query()
            ->where('route_slug', $slug)
            ->where('is_published', true)
            ->with([
                'chapters' => fn ($query) => $query->orderBy('order'),
                'chapters.steps' => fn ($query) => $query->orderBy('order'),
                'walkthroughContributions' => fn ($query) => $query
                    ->published()
                    ->with('author')
                    ->withCount('steps')
                    ->latest('published_at'),
            ])
            ->firstOrFail();

        return view('games.show', compact('game'));
    }
}
