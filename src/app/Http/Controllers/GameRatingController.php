<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GameRatingController extends Controller
{
    public function update(Request $request, Game $game): RedirectResponse
    {
        abort_unless($game->is_published, 404);

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
        ]);

        auth()->user()->gameRatings()->updateOrCreate(
            ['game_id' => $game->id],
            ['rating' => $validated['rating']],
        );

        return back()->with('status', 'Rating untuk ' . $game->title . ' berhasil disimpan.');
    }

    public function destroy(Game $game): RedirectResponse
    {
        auth()->user()->gameRatings()
            ->where('game_id', $game->id)
            ->delete();

        return back()->with('status', 'Rating untuk ' . $game->title . ' dihapus.');
    }
}
