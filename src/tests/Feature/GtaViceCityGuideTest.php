<?php

use App\Models\Game;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);
});

test('gta vice city is not created automatically', function () {
    expect(Game::where('slug', 'gta-vice-city')->exists())->toBeFalse();

    $this->get('/')
        ->assertOk()
        ->assertDontSee('GTA Vice City');
});

test('admin managed gta game appears dynamically without hardcoded walkthrough', function () {
    $game = Game::create([
        'title' => 'GTA Vice City',
        'slug' => 'gta-vice-city',
        'route_slug' => 'gta-vice-city',
        'description' => 'Walkthrough GTA Vice City yang dikelola melalui database.',
        'cover_image' => 'coverimg/GTA_Vice_City.png',
        'is_published' => true,
    ]);

    $this->get('/')
        ->assertOk()
        ->assertSee('GTA Vice City Walkthrough')
        ->assertSee('Upcoming');

    $this->get(route('games.show', ['slug' => $game->route_slug]))
        ->assertOk()
        ->assertSee('Walkthrough Dalam Persiapan');
});

test('admin managed gta chapters and steps render in the walkthrough layout', function () {
    $game = Game::create([
        'title' => 'GTA Vice City',
        'slug' => 'gta-vice-city',
        'route_slug' => 'gta-vice-city',
        'cover_image' => 'coverimg/GTA_Vice_City.png',
        'is_published' => true,
    ]);

    $chapter = $game->chapters()->create([
        'chapter_title' => 'Ken Rosenberg Missions',
        'slug' => 'ken-rosenberg-missions',
        'section_title' => 'Walkthrough',
        'order' => 1,
    ]);

    $chapter->steps()->create([
        'step_title' => 'The Party',
        'content' => '<p>Datangi kantor Ken untuk memulai misi.</p>',
        'order' => 1,
    ]);

    $this->get(route('games.show', ['slug' => $game->route_slug]))
        ->assertRedirect(route('games.walkthrough.show', [
            'gameSlug' => $game->route_slug,
            'chapterSlug' => $chapter->slug,
        ]));

    $this->get(route('games.walkthrough.show', [
        'gameSlug' => $game->route_slug,
        'chapterSlug' => $chapter->slug,
    ]))
        ->assertOk()
        ->assertSee('Ken Rosenberg Missions')
        ->assertSee('The Party')
        ->assertSee('Datangi kantor Ken untuk memulai misi.');
});
