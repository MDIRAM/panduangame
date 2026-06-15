<?php

use App\Models\Chapter;
use App\Models\Game;
use App\Models\Step;
use App\Models\User;
use App\Models\WalkthroughContribution;
use App\Filament\Admin\Resources\UserResource\Pages\EditUser;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

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
        ->assertSee('Database Only Game')
        ->assertSee('GTA Vice City')
        ->assertSee('Upcoming');

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

test('official rich text renders as html on the sidebar walkthrough', function () {
    $game = Game::where('slug', 'elden-ring')->firstOrFail();
    $chapter = $game->chapters()->with('steps')->firstOrFail();
    $step = $chapter->steps->firstOrFail();

    $chapter->update(['overview' => null]);
    $step->update([
        'content' => '<p>Gunakan <strong>Site of Grace</strong> sebelum lanjut.</p>',
    ]);

    $this->get(route('games.walkthrough.show', [
        'gameSlug' => $game->route_slug,
        'chapterSlug' => $chapter->slug,
    ]))
        ->assertOk()
        ->assertSee('<strong>Site of Grace</strong>', false);
});

test('registration stores a member user without contributor or admin access', function () {
    $this->post('/register', [
        'name' => 'Regular Player',
        'email' => 'player@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertRedirect(route('home'));

    $user = User::where('email', 'player@example.com')->firstOrFail();

    expect($user->hasRole('member'))->toBeTrue()
        ->and($user->hasRole('contributor'))->toBeFalse()
        ->and($user->canAccessPanel(filament()->getPanel('admin')))->toBeFalse();

    $this->actingAs($user)
        ->get('/admin')
        ->assertForbidden();

    $this->actingAs($user)
        ->get(route('contributions.create'))
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
        ->assertSee('Contributor User')
        ->assertSee('Member User');

    $member = User::where('email', 'member@admin.com')->firstOrFail();

    $this->actingAs($admin)
        ->get('/admin/users/'.$member->id.'/edit')
        ->assertOk()
        ->assertSee('Access role');

    $this->actingAs($admin)
        ->get('/admin/shield/roles')
        ->assertOk();

    $this->assertDatabaseHas('roles', [
        'name' => 'contributor',
        'guard_name' => 'web',
    ]);

    $this->assertDatabaseHas('roles', [
        'name' => 'member',
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

test('shared login returns non admin users to the public homepage', function () {
    $this->post('/login', [
        'email' => 'contributor@admin.com',
        'password' => 'password',
    ])->assertRedirect(route('home'));

    $this->get('/')
        ->assertOk()
        ->assertSee('Contributor Dashboard')
        ->assertDontSee('My Contributions')
        ->assertDontSee('Add Walkthrough')
        ->assertDontSee('Admin Panel');
});

test('contributor uses frontend contribution tools without admin panel access', function () {
    $contributor = User::where('email', 'contributor@admin.com')->firstOrFail();

    $this->actingAs($contributor)
        ->get('/')
        ->assertOk()
        ->assertSee('Contributor Dashboard')
        ->assertSee('My Account')
        ->assertSee('Write a guide')
        ->assertDontSee('My Contributions')
        ->assertDontSee('Add Walkthrough')
        ->assertDontSee('Admin Panel');

    $this->actingAs($contributor)
        ->get(route('contributions.index'))
        ->assertOk()
        ->assertSee('Contributor Dashboard')
        ->assertSee('Write a guide');

    $this->actingAs($contributor)
        ->get(route('contributions.create'))
        ->assertOk();

    $this->actingAs($contributor)
        ->get('/admin')
        ->assertForbidden();
});

test('super admin can promote a member to contributor from the user resource', function () {
    $admin = User::where('email', 'admin@admin.com')->firstOrFail();
    $member = User::where('email', 'member@admin.com')->firstOrFail();
    $contributorRole = Role::where('name', 'contributor')->firstOrFail();

    Livewire::actingAs($admin)
        ->test(EditUser::class, ['record' => $member->getRouteKey()])
        ->fillForm([
            'roles' => [$contributorRole->id],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($member->fresh()->hasRole('contributor'))->toBeTrue()
        ->and($member->fresh()->hasRole('member'))->toBeFalse();
});

test('member dashboard explains account access without admin statistics', function () {
    $member = User::where('email', 'member@admin.com')->firstOrFail();

    $this->actingAs($member)
        ->get('/dashboard')
        ->assertOk()
        ->assertSee('My Account')
        ->assertSee('Akun Member')
        ->assertSee($member->email)
        ->assertSee('Browse Guides')
        ->assertDontSee('Contribution status')
        ->assertDontSee('Published games')
        ->assertDontSee('Guide steps');
});

test('contributor account page shows profile access without contribution list', function () {
    $contributor = User::where('email', 'contributor@admin.com')->firstOrFail();

    $this->actingAs($contributor)
        ->get('/dashboard')
        ->assertOk()
        ->assertSee('My Account')
        ->assertSee('Akun Contributor')
        ->assertSee($contributor->email)
        ->assertSee('Contributor Dashboard')
        ->assertDontSee('Contribution status')
        ->assertDontSee('Kontribusi terbaru')
        ->assertDontSee('Published games')
        ->assertDontSee('Guide steps');
});

test('contributor dashboard lists their contribution workflow', function () {
    $contributor = User::where('email', 'contributor@admin.com')->firstOrFail();
    $game = Game::where('is_published', true)->firstOrFail();

    foreach (WalkthroughContribution::statuses() as $status => $label) {
        $contributor->walkthroughContributions()->create([
            'game_id' => $game->id,
            'title' => "Contributor {$label}",
            'slug' => "contributor-{$status}",
            'summary' => "Walkthrough berstatus {$label}.",
            'status' => $status,
        ]);
    }

    $this->actingAs($contributor)
        ->get(route('contributions.index'))
        ->assertOk()
        ->assertSee('Contributor Dashboard')
        ->assertSee('Write a guide')
        ->assertSee('Contributor Draft')
        ->assertSee('Contributor Pending review')
        ->assertSee('Contributor Published')
        ->assertSee('Contributor Rejected')
        ->assertDontSee('Published games');
});
