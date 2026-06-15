<?php

use App\Http\Controllers\Api\GameController;
use App\Http\Controllers\Api\CommunityGuideController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::get('/games', [GameController::class, 'index'])->name('games.index');
    Route::get('/games/{game:route_slug}', [GameController::class, 'show'])->name('games.show');
    Route::get('/games/{game:route_slug}/community-guides', [CommunityGuideController::class, 'index'])
        ->name('games.community-guides.index');
    Route::get('/community-guides/{contribution}', [CommunityGuideController::class, 'show'])
        ->name('community-guides.show');
    Route::get(
        '/games/{game:route_slug}/chapters/{chapter:slug}',
        [GameController::class, 'chapter'],
    )->scopeBindings()->name('games.chapters.show');
});
