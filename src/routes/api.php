<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GameController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::post('/auth/register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login');

    Route::get('/games', [GameController::class, 'index'])->name('games.index');
    Route::get('/games/{game:route_slug}', [GameController::class, 'show'])->name('games.show');
    Route::get(
        '/games/{game:route_slug}/chapters/{chapter:slug}',
        [GameController::class, 'chapter'],
    )->scopeBindings()->name('games.chapters.show');

    Route::middleware('api.token')->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me'])->name('auth.me');
        Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');

    });
});
