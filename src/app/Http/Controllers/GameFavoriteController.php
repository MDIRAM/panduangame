<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\RedirectResponse;

class GameFavoriteController extends Controller
{
    public function store(Game $game): RedirectResponse
    {
        abort_unless($game->is_published, 404);

        auth()->user()->gameFavorites()->firstOrCreate([
            'game_id' => $game->id,
        ]);

        return back()->with('status', $game->title . ' ditambahkan ke favorite.');
    }

    public function destroy(Game $game): RedirectResponse
    {
        auth()->user()->gameFavorites()
            ->where('game_id', $game->id)
            ->delete();

        return back()->with('status', $game->title . ' dihapus dari favorite.');
    }
}
