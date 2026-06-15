<?php

use App\Models\Game;
use App\Models\User;
use App\Models\WalkthroughContribution;
use App\Filament\Admin\Resources\WalkthroughContributionResource;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);

    $this->game = Game::create([
        'title' => 'Contribution Test Game',
        'slug' => 'contribution-test-game',
        'route_slug' => 'contribution-test',
        'description' => 'A published game for community guide tests.',
        'is_published' => true,
    ]);

    $this->user = User::factory()->create();
    $this->user->assignRole('contributor');
});

test('guest can only read published community walkthroughs', function () {
    $contribution = WalkthroughContribution::create([
        'user_id' => $this->user->id,
        'game_id' => $this->game->id,
        'title' => 'Hidden Draft',
        'slug' => 'hidden-draft',
        'summary' => 'This guide is still a private draft and must not be public.',
        'status' => WalkthroughContribution::STATUS_DRAFT,
    ]);

    $this->get(route('contributions.show', $contribution))->assertNotFound();
    $this->getJson('/api/v1/community-guides/'.$contribution->slug)->assertNotFound();
    $this->get(route('contributions.create'))->assertRedirect(route('login'));

    $contribution->update(['status' => WalkthroughContribution::STATUS_PUBLISHED]);
    $contribution->steps()->create([
        'title' => 'First Public Step',
        'content' => 'A complete walkthrough step that is safe for public readers.',
        'order' => 1,
    ]);

    $this->get(route('contributions.show', $contribution))
        ->assertOk()
        ->assertSee('First Public Step')
        ->assertSee('By '.$this->user->name);

    $this->getJson('/api/v1/community-guides/'.$contribution->slug)
        ->assertOk()
        ->assertJsonPath('data.title', 'Hidden Draft')
        ->assertJsonPath('data.steps.0.title', 'First Public Step');
});

test('logged in user can create edit and submit their own walkthrough', function () {
    Storage::fake('public');

    $response = $this->actingAs($this->user)->post(route('contributions.store'), [
        'game_id' => $this->game->id,
        'title' => 'My First Route',
        'summary' => 'A practical route written by a community member for this game.',
    ]);

    $contribution = WalkthroughContribution::where('title', 'My First Route')->firstOrFail();

    $response->assertRedirect(route('contributions.edit', $contribution));

    $this->actingAs($this->user)->post(route('contribution-steps.store', $contribution), [
        'title' => 'Reach the First Checkpoint',
        'content' => 'Follow the main path carefully and activate the first checkpoint.',
        'order' => 1,
        'image' => UploadedFile::fake()->image('checkpoint.jpg', 800, 450),
    ])->assertRedirect();

    $step = $contribution->steps()->firstOrFail();
    Storage::disk('public')->assertExists($step->image_path);

    $this->actingAs($this->user)
        ->post(route('contributions.submit', $contribution))
        ->assertRedirect(route('contributions.index'));

    expect($contribution->fresh()->status)
        ->toBe(WalkthroughContribution::STATUS_PENDING);
});

test('a user cannot modify another users contribution', function () {
    $owner = User::factory()->create();
    $owner->assignRole('contributor');

    $contribution = WalkthroughContribution::create([
        'user_id' => $owner->id,
        'game_id' => $this->game->id,
        'title' => 'Another User Guide',
        'slug' => 'another-user-guide',
        'summary' => 'This contribution belongs to a different account and is protected.',
        'status' => WalkthroughContribution::STATUS_DRAFT,
    ]);

    $this->actingAs($this->user)
        ->get(route('contributions.edit', $contribution))
        ->assertForbidden();

    $this->actingAs($this->user)
        ->put(route('contributions.update', $contribution), [
            'game_id' => $this->game->id,
            'title' => 'Stolen Guide',
            'summary' => 'This update must never be accepted by the application policy.',
        ])
        ->assertForbidden();

    expect($contribution->fresh()->title)->toBe('Another User Guide');
});

test('published contribution is listed on the game page and locked for its author', function () {
    $contribution = WalkthroughContribution::create([
        'user_id' => $this->user->id,
        'game_id' => $this->game->id,
        'title' => 'Published Community Route',
        'slug' => 'published-community-route',
        'summary' => 'A reviewed contribution that should appear on the public game page.',
        'status' => WalkthroughContribution::STATUS_PUBLISHED,
    ]);

    $contribution->steps()->create([
        'title' => 'Published Step',
        'content' => 'This published step is displayed to every visitor of the website.',
        'order' => 1,
    ]);

    $this->get('/games/contribution-test')
        ->assertOk()
        ->assertSee('Community Walkthroughs')
        ->assertSee('Published Community Route');

    $this->actingAs($this->user)
        ->delete(route('contributions.destroy', $contribution))
        ->assertForbidden();
});

test('super admin can open the contribution moderation resource', function () {
    $admin = User::factory()->create();
    $admin->assignRole('super_admin');

    $contribution = WalkthroughContribution::create([
        'user_id' => $this->user->id,
        'game_id' => $this->game->id,
        'title' => 'Submission for Review',
        'slug' => 'submission-for-review',
        'summary' => 'A pending walkthrough submission that an administrator can review.',
        'status' => WalkthroughContribution::STATUS_PENDING,
        'submitted_at' => now(),
    ]);

    $this->actingAs($admin)
        ->get('/admin/walkthrough-contributions')
        ->assertOk();

    $this->actingAs($admin)
        ->get(WalkthroughContributionResource::getUrl('edit', ['record' => $contribution]))
        ->assertOk()
        ->assertSee('Submission for Review');
});
