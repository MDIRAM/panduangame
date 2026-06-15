<?php

use App\Models\Chapter;
use Database\Seeders\GameSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(GameSeeder::class);
});

test('persona story index renders chapter navigation from database', function () {
    $response = $this->get('/games/persona-3');

    $response
        ->assertStatus(200)
        ->assertSee('Story Mission Walkthroughs')
        ->assertSee('Prologue (April 7 - April 18) Walkthrough')
        ->assertSee('First Visit to Tartarus (April 19 - April 20) Walkthrough')
        ->assertSee('Final Mission: The Promised Day (January 31)');
});

test('prologue detail renders database steps sidebar and local images', function () {
    $response = $this->get('/games/persona-3/story/prologue-april-7-april-18');

    $response
        ->assertStatus(200)
        ->assertSee('Story Mission Walkthroughs')
        ->assertSee('Iwatodai Station')
        ->assertSee('coverimg/Persona3/1.png', false);
});

test('tartarus guide is seeded from deployable local json content', function () {
    $chapter = Chapter::where('slug', 'first-visit-to-tartarus-april-19-april-20')
        ->with('steps')
        ->firstOrFail();

    expect($chapter->steps)
        ->toHaveCount(29)
        ->and($chapter->source_url)
        ->toBe('https://www.ign.com/wikis/persona-3-reload/First_Visit_to_Tartarus_(April_19_-_April_20)_Walkthrough')
        ->and($chapter->steps->first()->step_title)
        ->toBe('Menuju Tartarus')
        ->and($chapter->steps->firstWhere('order', 22)?->step_title)
        ->toBe('All-Out Attack Pertama')
        ->and($chapter->steps->firstWhere('order', 1)?->image_url)
        ->toContain('oyster.ignimgs.com');

    $this->get('/games/persona-3/story/first-visit-to-tartarus-april-19-april-20')
        ->assertOk()
        ->assertSee('Briefing dari Akihiko')
        ->assertSee('All-Out Attack Pertama')
        ->assertSee('Persona_3_Reload_20240119000900.jpg', false);
});
