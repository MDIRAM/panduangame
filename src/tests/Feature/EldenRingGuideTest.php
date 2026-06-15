<?php

use App\Models\Game;
use Database\Seeders\EldenRingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(EldenRingSeeder::class);
});

test('elden ring game page displays the seeded progress route', function () {
    $this->get('/games/elden-ring')
        ->assertOk()
        ->assertSee('The Lands Between Walkthrough')
        ->assertSee('Limgrave Barat: Awal Perjalanan')
        ->assertSee('Limgrave Timur: Mistwood dan Fort Haight')
        ->assertSee('Stormveil Castle: Margit dan Godrick')
        ->assertSee('coverimg/EldenRing/west-limgrave/renna.jpg');
});

test('elden ring chapters contain ordered steps and navigation', function () {
    $this->get('/games/elden-ring/walkthrough/west-limgrave')
        ->assertOk()
        ->assertSee('Selesaikan Cave of Knowledge')
        ->assertSee('Kunjungi Church of Elleh')
        ->assertSee('Up Next: Limgrave Timur: Mistwood dan Fort Haight')
        ->assertSee('coverimg/EldenRing/west-limgrave/tutorial.jpg');

    $this->get('/games/elden-ring/walkthrough/stormveil-castle')
        ->assertOk()
        ->assertSee('Kalahkan Margit, the Fell Omen')
        ->assertSee('Kalahkan Godrick the Grafted')
        ->assertSee('coverimg/EldenRing/stormveil-castle/godrick.jpg')
        ->assertSee('Previous');
});

test('elden ring walkthrough is exposed through the public api', function () {
    $this->getJson('/api/v1/games/elden-ring')
        ->assertOk()
        ->assertJsonPath('data.slug', 'elden-ring')
        ->assertJsonPath('data.chapters.0.slug', 'west-limgrave')
        ->assertJsonPath('data.chapters.2.slug', 'stormveil-castle');

    expect(Game::where('slug', 'elden-ring')->firstOrFail()->chapters()->count())->toBe(3);
});
