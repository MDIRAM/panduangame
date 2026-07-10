<?php

use App\Models\Game;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->user = User::where('email', 'member@admin.com')->firstOrFail();
    $this->game = Game::where('slug', 'persona-3-reload')->firstOrFail();
});

test('guest must login before favoriting or rating a game', function () {
    $this->post(route('games.favorite.store', $this->game))->assertRedirect(route('login'));
    $this->put(route('games.rating.update', $this->game), ['rating' => 5])
        ->assertRedirect(route('login'));
});

test('user can favorite a game once and remove it', function () {
    $this->actingAs($this->user)
        ->post(route('games.favorite.store', $this->game))
        ->assertRedirect();

    $this->actingAs($this->user)
        ->post(route('games.favorite.store', $this->game))
        ->assertRedirect();

    expect($this->user->gameFavorites()->where('game_id', $this->game->id)->count())->toBe(1);

    $this->actingAs($this->user)
        ->delete(route('games.favorite.destroy', $this->game))
        ->assertRedirect();

    $this->assertDatabaseMissing('game_favorites', [
        'user_id' => $this->user->id,
        'game_id' => $this->game->id,
    ]);
});

test('user has one editable rating per game', function () {
    $this->actingAs($this->user)
        ->put(route('games.rating.update', $this->game), ['rating' => 3])
        ->assertRedirect();

    $this->actingAs($this->user)
        ->put(route('games.rating.update', $this->game), ['rating' => 5])
        ->assertRedirect();

    $this->assertDatabaseCount('game_ratings', 1);
    $this->assertDatabaseHas('game_ratings', [
        'user_id' => $this->user->id,
        'game_id' => $this->game->id,
        'rating' => 5,
    ]);

    $this->actingAs($this->user)
        ->put(route('games.rating.update', $this->game), ['rating' => 6])
        ->assertSessionHasErrors('rating');
});

test('favorite and rating status are rendered dynamically', function () {
    $this->user->gameFavorites()->create(['game_id' => $this->game->id]);
    $this->user->gameRatings()->create(['game_id' => $this->game->id, 'rating' => 4]);

    $chapter = $this->game->chapters()->orderBy('order')->firstOrFail();

    $this->actingAs($this->user)
        ->get(route('games.walkthrough.show', [
            'gameSlug' => $this->game->route_slug,
            'chapterSlug' => $chapter->slug,
        ]))
        ->assertOk()
        ->assertSee('4.0/5')
        ->assertSee('Remove favorite')
        ->assertSee('id="game-rating-4"', false)
        ->assertSee('checked', false);

    $this->actingAs($this->user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee($this->game->title)
        ->assertSee('Your rating 4/5')
        ->assertSee($this->game->cover_url, false);
});
