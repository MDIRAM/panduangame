<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChapterResource;
use App\Http\Resources\GameResource;
use App\Models\Chapter;
use App\Models\Game;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GameController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $games = Game::query()
            ->where('is_published', true)
            ->withCount('chapters')
            ->orderBy('title')
            ->paginate(12);

        return GameResource::collection($games);
    }

    public function show(Game $game): GameResource
    {
        abort_unless($game->is_published, 404);

        $game->load([
            'chapters' => fn ($query) => $query->orderBy('order'),
        ])->loadCount('chapters');

        return new GameResource($game);
    }

    public function chapter(Game $game, Chapter $chapter): ChapterResource
    {
        abort_unless(
            $game->is_published && $chapter->game_id === $game->id,
            404,
        );

        $chapter->load([
            'game',
            'steps' => fn ($query) => $query->orderBy('order'),
        ])->loadCount('steps');

        return new ChapterResource($chapter);
    }
}
