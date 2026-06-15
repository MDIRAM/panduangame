<?php

use App\Models\Game;
use Database\Seeders\GtaViceCitySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(GtaViceCitySeeder::class);
});

test('gta vice city cover opens the first sidebar chapter', function () {
    $game = Game::where('slug', 'gta-vice-city')->with('chapters.steps')->firstOrFail();

    expect($game->chapters)->toHaveCount(7);

    $this->get('/games/gta-vice-city')
        ->assertRedirect('/games/gta-vice-city/walkthrough/in-the-beginning-and-an-old-friend');

    $this->followingRedirects()
        ->get('/games/gta-vice-city')
        ->assertOk()
        ->assertSee('Walkthrough Chapters')
        ->assertSee('In the Beginning... &amp; An Old Friend', false)
        ->assertSee('Ken Rosenberg Missions')
        ->assertSee('Tiba di Vice City')
        ->assertSee('An Old Friend')
        ->assertSee('aria-current="page"', false);
});

test('gta vice city template chapters can exist before walkthrough content is filled', function () {
    $this->get('/games/gta-vice-city/walkthrough/ken-rosenberg-missions')
        ->assertOk()
        ->assertSee('Ken Rosenberg Missions')
        ->assertSee('Konten belum tersedia')
        ->assertSee('In the Beginning... &amp; An Old Friend', false);
});
