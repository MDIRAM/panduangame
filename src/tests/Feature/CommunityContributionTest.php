<?php

use App\Models\Game;
use App\Models\User;
use App\Models\WalkthroughContribution;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);

    $this->game = Game::create([
        'title' => 'Official Test Game',
        'slug' => 'official-test-game',
        'route_slug' => 'official-test',
        'is_published' => true,
    ]);

    $this->chapter = $this->game->chapters()->create([
        'chapter_title' => 'Opening Route',
        'slug' => 'opening-route',
        'section_title' => 'Walkthrough',
        'order' => 1,
    ]);
});

test('frontend and api contribution routes are disabled', function () {
    expect(Route::has('contributions.index'))->toBeFalse()
        ->and(Route::has('contributions.create'))->toBeFalse()
        ->and(Route::has('contributions.store'))->toBeFalse()
        ->and(Route::has('contributions.show'))->toBeFalse();

    $this->get('/dashboard/walkthroughs')->assertNotFound();
    $this->postJson('/api/v1/me/contributions')->assertNotFound();
    $this->getJson('/api/v1/community-guides/example')->assertNotFound();
});

test('legacy contribution records remain stored but are not rendered publicly', function () {
    $user = User::factory()->create();
    $contribution = WalkthroughContribution::create([
        'user_id' => $user->id,
        'game_id' => $this->game->id,
        'chapter_id' => $this->chapter->id,
        'title' => 'Archived Community Route',
        'slug' => 'archived-community-route',
        'summary' => 'Legacy data retained after simplifying the product flow.',
        'status' => WalkthroughContribution::STATUS_PUBLISHED,
    ]);

    $this->assertDatabaseHas('walkthrough_contributions', ['id' => $contribution->id]);

    $this->get('/games/official-test/walkthrough/opening-route')
        ->assertOk()
        ->assertDontSee('Archived Community Route')
        ->assertDontSee('Community Contributions')
        ->assertDontSee('Buat walkthrough versi kamu');
});

test('legacy contribution resource is unavailable from the admin workflow', function () {
    $admin = User::factory()->create();
    $admin->assignRole('super_admin');

    $this->actingAs($admin)
        ->get('/admin/walkthrough-contributions')
        ->assertNotFound();
});
