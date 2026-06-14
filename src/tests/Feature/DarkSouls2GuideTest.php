<?php

use App\Models\Game;
use Database\Seeders\DarkSouls2Seeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DarkSouls2Seeder::class);
});

test('dark souls 2 progress route is rendered from database', function () {
    $game = Game::where('slug', 'dark-souls-2')->with('chapters.steps')->firstOrFail();

    expect($game->chapters)->toHaveCount(19);

    $this->get('/games/dark-souls-2')
        ->assertOk()
        ->assertSee('Game Progress Route')
        ->assertSee('Things Betwixt')
        ->assertSee('Doors of Pharros')
        ->assertSee('coverimg/DS2/Majula.png', false)
        ->assertSee('coverimg/DS2/Forest of Fallen Giants.png', false)
        ->assertSee('coverimg/DS2/heides_tower_of_flame_cathedral_of_blue.png', false)
        ->assertDontSee('coverimg/DS2/majula/map.jpg', false)
        ->assertDontSee('coverimg/DS2/forest-of-fallen-giants/map.jpg', false)
        ->assertDontSee('coverimg/DS2/heides-tower/map.jpg', false)
        ->assertSee('Seluruh data berasal dari database');
});

test('dark souls 2 area detail renders steps image and navigation', function () {
    $this->get('/games/dark-souls-2/walkthrough/forest-of-fallen-giants')
        ->assertOk()
        ->assertSee('Back to Game Progress Route')
        ->assertSee('Rooftops, Cale, and First Shortcut')
        ->assertSee("Ballista Room, Pharros' Lock, and Pate")
        ->assertSee('Seaside Sword Room and Second Shortcut')
        ->assertSee('Flame Lizard Pit')
        ->assertSee('coverimg/DS2/forest-of-fallen-giants/cale.jpg', false)
        ->assertSee('Majula')
        ->assertSee("Heide's Tower of Flame");
});

test('heides tower follows fextralife branches and no mans wharf route', function () {
    $this->get('/games/dark-souls-2/walkthrough/heides-tower')
        ->assertOk()
        ->assertSee('Abundance of Old Knights')
        ->assertSee('To the Dragonslayer')
        ->assertSee('To the Dragonrider')
        ->assertSee("To No Man's Wharf")
        ->assertSee('Guardian Dragon')
        ->assertSee('Licia of Lindeldt')
        ->assertSee('coverimg/DS2/heides-tower/dragon.jpg', false)
        ->assertSee('Up Next: Cathedral of Blue');
});

test('cathedral of blue renders old dragonslayer and targray route', function () {
    $this->get('/games/dark-souls-2/walkthrough/cathedral-of-blue')
        ->assertOk()
        ->assertSee('Cathedral of Blue Walkthrough')
        ->assertSee('Old Dragonslayer Boss Fight')
        ->assertSee('Strategy')
        ->assertSee('Boss Rewards')
        ->assertSee('Blue Sentinel Targray')
        ->assertSee('Token of Fidelity')
        ->assertSee('coverimg/DS2/cathedral-of-blue/old-dragonslayer.jpg', false)
        ->assertSee('coverimg/DS2/cathedral-of-blue/targray.jpg', false)
        ->assertSee("Previous")
        ->assertSee("Heide's Tower of Flame")
        ->assertSee("Up Next: No-Man's Wharf");
});

test('things betwixt includes detailed route items and optional encounters', function () {
    $this->get('/games/dark-souls-2/walkthrough/things-betwixt')
        ->assertOk()
        ->assertSee('Self Recollection')
        ->assertSee('Learning the Ropes')
        ->assertSee('Optional Paths and Rewards')
        ->assertSee('chapter-overview', false)
        ->assertSee('tiba di Majula')
        ->assertSee('Smooth &amp; Silky Stone', false)
        ->assertSee("Handmaid's Ladle")
        ->assertSee('oyster.ignimgs.com/mediawiki/apis.ign.com/dark-souls-2/4/44/Cyclops.jpg', false);
});

test('majula renders hub overview essentials and route navigation', function () {
    $this->get('/games/dark-souls-2/walkthrough/majula')
        ->assertOk()
        ->assertSee('Majula adalah kota pesisir')
        ->assertSee('Cavern Detour')
        ->assertSee('Scenic Route to the Bonfire')
        ->assertSee('Meeting the Locals')
        ->assertSee('The Mansion of Majula')
        ->assertSee('The Pit to the Gutter')
        ->assertSee("Entrance to Heide's Tower of Flame")
        ->assertSee('Entrance to Forest of Fallen Giants')
        ->assertSee('coverimg/DS2/majula/entering.jpg', false)
        ->assertSee('coverimg/DS2/majula/to-forest.jpg', false)
        ->assertSee('Up Next: Forest of Fallen Giants');
});

test('dark souls 2 chapter route is scoped to its game', function () {
    $this->get('/games/persona-3-reload/walkthrough/things-betwixt')
        ->assertNotFound();
});
