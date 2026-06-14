<?php

use App\Models\Chapter;
use App\Services\PersonaCsvImporter;
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

test('csv importer maps walkthrough content and image url into tartarus steps', function () {
    $chapter = Chapter::where('slug', 'first-visit-to-tartarus-april-19-april-20')->firstOrFail();
    $csvPath = tempnam(sys_get_temp_dir(), 'persona-guide-');

    file_put_contents(
        $csvPath,
        "content,image_url\n"
        ."Enter Tartarus and meet the team,https://assets-prd.ignimgs.com/tartarus-entry.jpg\n",
    );

    app(PersonaCsvImporter::class)->import($chapter, $csvPath);

    unlink($csvPath);

    $response = $this->get('/games/persona-3/story/first-visit-to-tartarus-april-19-april-20');

    $response
        ->assertStatus(200)
        ->assertSee('Enter Tartarus and meet the team')
        ->assertSee('https://assets-prd.ignimgs.com/tartarus-entry.jpg', false);
});
