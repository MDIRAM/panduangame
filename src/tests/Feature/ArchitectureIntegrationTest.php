<?php

use App\Models\Chapter;
use App\Models\Game;
use App\Models\Step;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

test('catalog and game pages are driven by published database records', function () {
    Game::create([
        'title' => 'Database Only Game',
        'slug' => 'database-only-game',
        'route_slug' => 'database-only',
        'description' => 'Created directly in the database.',
        'cover_image' => 'coverimg/EldenRing.png',
        'is_published' => true,
    ]);

    $this->get('/')
        ->assertOk()
        ->assertSee('Database Only Game');

    $this->get('/games/database-only')
        ->assertOk()
        ->assertSee('Created directly in the database.');
});

test('public api exposes games chapters and ordered steps', function () {
    $this->getJson('/api/v1/games')
        ->assertOk()
        ->assertJsonPath('data.0.title', 'Dark Souls 2');

    $this->getJson('/api/v1/games/persona-3')
        ->assertOk()
        ->assertJsonPath('data.slug', 'persona-3')
        ->assertJsonPath('data.chapters.0.slug', 'prologue-april-7-april-18');

    $this->getJson('/api/v1/games/persona-3/chapters/first-visit-to-tartarus-april-19-april-20')
        ->assertOk()
        ->assertJsonPath('data.steps.0.order', 1)
        ->assertJsonPath('data.steps.0.title', 'Menuju Tartarus');
});

test('chapter slugs are unique per game instead of globally', function () {
    $eldenRing = Game::where('slug', 'elden-ring')->firstOrFail();
    $persona = Game::where('slug', 'persona-3-reload')->firstOrFail();

    Chapter::create([
        'game_id' => $eldenRing->id,
        'chapter_title' => 'Shared Route',
        'slug' => 'shared-route',
        'order' => 1,
    ]);

    $personaChapter = Chapter::create([
        'game_id' => $persona->id,
        'chapter_title' => 'Shared Route',
        'slug' => 'shared-route',
        'order' => 99,
    ]);

    expect($personaChapter)->toBeInstanceOf(Chapter::class);
});

test('walkthrough content supports create update and cascading delete', function () {
    $game = Game::create([
        'title' => 'CRUD Test Game',
        'slug' => 'crud-test-game',
        'route_slug' => 'crud-test',
        'is_published' => true,
    ]);

    $chapter = $game->chapters()->create([
        'chapter_title' => 'Opening Area',
        'slug' => 'opening-area',
        'order' => 1,
    ]);

    $step = $chapter->steps()->create([
        'step_title' => 'First Step',
        'content' => 'Initial content',
        'order' => 1,
    ]);

    $step->update(['content' => 'Updated content']);

    expect($step->fresh()->content)->toBe('Updated content');

    $game->delete();

    $this->assertDatabaseMissing('chapters', ['id' => $chapter->id]);
    $this->assertDatabaseMissing('steps', ['id' => $step->id]);
});

test('seeded reviews are related to games and authors', function () {
    expect(\App\Models\Review::whereNull('game_id')->count())->toBe(0)
        ->and(\App\Models\Review::whereNull('user_id')->count())->toBe(0);
});

test('registration stores a contributor user and blocks admin panel access', function () {
    $this->post('/register', [
        'name' => 'Regular Player',
        'email' => 'player@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertRedirect('/dashboard');

    $user = User::where('email', 'player@example.com')->firstOrFail();

    expect($user->hasRole('contributor'))->toBeTrue()
        ->and($user->roles()->where('name', 'contributor')->exists())->toBeTrue()
        ->and($user->canAccessPanel(filament()->getPanel('admin')))->toBeFalse();

    $this->actingAs($user)
        ->get('/admin')
        ->assertForbidden();
});

test('super admin can open walkthrough crud pages', function () {
    $admin = User::where('email', 'admin@admin.com')->firstOrFail();

    $this->actingAs($admin)
        ->get('/admin/games')
        ->assertOk();

    $this->actingAs($admin)
        ->get('/admin/chapters')
        ->assertOk();

    $this->actingAs($admin)
        ->get('/admin/steps')
        ->assertOk();

    $this->actingAs($admin)
        ->get('/admin/users')
        ->assertOk()
        ->assertSee('Contributor User');

    $this->actingAs($admin)
        ->get('/admin/shield/roles')
        ->assertOk();

    $this->assertDatabaseHas('roles', [
        'name' => 'contributor',
        'guard_name' => 'web',
    ]);
});

test('shared login redirects admin to panel and keeps website navigation available', function () {
    $this->post('/login', [
        'email' => 'admin@admin.com',
        'password' => 'password',
    ])->assertRedirect('/admin');

    $this->get('/')
        ->assertOk()
        ->assertSee('Admin Panel')
        ->assertSee('Log out')
        ->assertDontSee('>Login<', false);

    $this->get('/admin')
        ->assertOk()
        ->assertSee('Lihat Website');
});

test('dashboard displays live database totals', function () {
    $user = User::where('email', 'contributor@admin.com')->firstOrFail();

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertOk()
        ->assertSee((string) Game::where('is_published', true)->count())
        ->assertSee((string) Chapter::count())
        ->assertSee((string) Step::count());
});
