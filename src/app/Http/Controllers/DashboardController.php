<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();

        $libraryGames = Game::query()
            ->where('is_published', true)
            ->where(function ($query) use ($user) {
                $query
                    ->whereHas('favorites', fn ($favoriteQuery) => $favoriteQuery->where('user_id', $user->id))
                    ->orWhereHas('ratings', fn ($ratingQuery) => $ratingQuery->where('user_id', $user->id));
            })
            ->withCount('ratings')
            ->withAvg('ratings', 'rating')
            ->withExists([
                'favorites as is_favorited' => fn ($query) => $query->where('user_id', $user->id),
            ])
            ->with([
                'ratings' => fn ($query) => $query->where('user_id', $user->id),
            ])
            ->orderBy('title')
            ->get();

        return view('dashboard', [
            'avatarUrl' => $user->getFilamentAvatarUrl(),
            'favoriteCount' => $user->gameFavorites()->count(),
            'libraryGames' => $libraryGames,
            'ratingCount' => $user->gameRatings()->count(),
            'roleLabel' => $user->hasRole('super_admin') ? 'Admin' : 'User',
        ]);
    }
}
