<?php

use App\Filament\Admin\Resources\ChapterResource;
use App\Filament\Admin\Resources\ChapterResource\Pages\CreateChapter;
use App\Filament\Admin\Resources\ChapterResource\Pages\EditChapter;
use App\Filament\Admin\Resources\ChapterResource\RelationManagers\StepsRelationManager;
use App\Filament\Admin\Resources\GameResource\Pages\CreateGame;
use App\Filament\Admin\Resources\GameResource\Pages\EditGame;
use App\Filament\Admin\Resources\GameResource\RelationManagers\ChaptersRelationManager;
use App\Filament\Admin\Resources\StepResource\Pages\CreateStep;
use App\Models\Chapter;
use App\Models\Game;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Filament\Tables\Actions\CreateAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('admin creates a complete walkthrough from filament without seeders', function () {
    Storage::fake('public');
    $this->seed(RoleSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole('super_admin');

    Livewire::actingAs($admin)
        ->test(CreateGame::class)
        ->fillForm([
            'title' => 'Admin Created Game',
            'slug' => 'admin-created-game',
            'route_slug' => 'admin-created-game',
            'theme_preset' => 'green',
            'subtitle' => 'Walkthrough dibuat sepenuhnya dari Filament.',
            'description' => 'Game ini tidak berasal dari Seeder.',
            'cover_image' => UploadedFile::fake()->image('game-cover.jpg', 1280, 720),
            'is_featured' => false,
            'is_published' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $game = Game::where('slug', 'admin-created-game')->firstOrFail();
    expect($game->theme_preset)->toBe('green');
    Storage::disk('public')->assertExists($game->cover_image);

    Livewire::actingAs($admin)
        ->test(CreateChapter::class)
        ->fillForm([
            'game_id' => $game->id,
            'order' => 1,
            'chapter_title' => 'Opening Mission',
            'slug' => 'opening-mission',
            'section_title' => 'Walkthrough',
            'overview' => ['Mulai perjalanan dari titik awal.'],
            'overview_image' => UploadedFile::fake()->image('chapter-overview.jpg', 1280, 720),
            'cover_image' => UploadedFile::fake()->image('chapter-cover.jpg', 1280, 720),
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $chapter = $game->chapters()->where('slug', 'opening-mission')->firstOrFail();
    Storage::disk('public')->assertExists($chapter->overview_image);
    Storage::disk('public')->assertExists($chapter->cover_image);

    Livewire::actingAs($admin)
        ->test(CreateStep::class)
        ->fillForm([
            'chapter_id' => $chapter->id,
            'order' => 1,
            'step_title' => 'Langkah Pertama',
            'content' => '<p>Ikuti marker utama lalu simpan progress.</p>',
            'image_url' => UploadedFile::fake()->image('step-image.jpg', 1280, 720),
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $step = $chapter->steps()->firstOrFail();
    Storage::disk('public')->assertExists($step->image_url);

    $this->get(route('games.walkthrough.show', [
        'gameSlug' => $game->route_slug,
        'chapterSlug' => $chapter->slug,
    ]))
        ->assertOk()
        ->assertSee('Opening Mission')
        ->assertSee('<p>Ikuti marker utama lalu simpan progress.</p>', false)
        ->assertSee($chapter->overview_image_url, false)
        ->assertSee($step->resolved_image_url, false);
});

test('admin continues a walkthrough through nested game and chapter panels', function () {
    $this->seed(RoleSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole('super_admin');

    $game = Game::create([
        'title' => 'Nested Admin Game',
        'slug' => 'nested-admin-game',
        'route_slug' => 'nested-admin-game',
        'is_published' => true,
    ]);
    Livewire::actingAs($admin)
        ->test(ChaptersRelationManager::class, [
            'ownerRecord' => $game,
            'pageClass' => EditGame::class,
        ])
        ->callTableAction(CreateAction::class, data: [
            'order' => 1,
            'chapter_title' => 'Next Main Area',
            'slug' => 'next-main-area',
            'section_title' => 'Walkthrough',
            'source_url' => 'https://example.com/walkthrough-source',
            'overview' => ['Area lanjutan yang dibuat dari panel Game.'],
        ])
        ->assertHasNoTableActionErrors();

    $chapter = $game->chapters()->where('slug', 'next-main-area')->firstOrFail();

    Livewire::actingAs($admin)
        ->test(StepsRelationManager::class, [
            'ownerRecord' => $chapter,
            'pageClass' => EditChapter::class,
        ])
        ->callTableAction(CreateAction::class, data: [
            'order' => 1,
            'step_title' => 'Lanjutkan Rute Utama',
            'content' => '<p>Ikuti jalan utama menuju objective berikutnya.</p>',
        ])
        ->assertHasNoTableActionErrors();

    $this->assertDatabaseHas('chapters', [
        'game_id' => $game->id,
        'slug' => 'next-main-area',
        'source_url' => 'https://example.com/walkthrough-source',
    ]);
    $this->assertDatabaseHas('steps', [
        'chapter_id' => $chapter->id,
        'step_title' => 'Lanjutkan Rute Utama',
    ]);

    $this->get(route('games.walkthrough.show', [
        'gameSlug' => $game->route_slug,
        'chapterSlug' => $chapter->slug,
    ]))
        ->assertOk()
        ->assertSee('Lanjutkan Rute Utama')
        ->assertSee('Reference source')
        ->assertSee('https://example.com/walkthrough-source', false);
});

test('walkthrough editor sidebar is scoped to one game and shows nested chapters', function () {
    $this->seed(RoleSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole('super_admin');

    $game = Game::create([
        'title' => 'Vice City Test',
        'slug' => 'vice-city-test',
        'route_slug' => 'vice-city-test',
        'is_published' => true,
    ]);
    $otherGame = Game::create([
        'title' => 'Other Game',
        'slug' => 'other-game',
        'route_slug' => 'other-game',
        'is_published' => true,
    ]);

    $parent = Chapter::create([
        'game_id' => $game->id,
        'chapter_title' => 'Ken Rosenberg Missions',
        'slug' => 'ken-rosenberg-missions',
        'order' => 1,
    ]);
    $child = Chapter::create([
        'game_id' => $game->id,
        'parent_id' => $parent->id,
        'chapter_title' => 'The Party',
        'slug' => 'the-party',
        'order' => 1,
    ]);
    Chapter::create([
        'game_id' => $otherGame->id,
        'chapter_title' => 'Foreign Chapter',
        'slug' => 'foreign-chapter',
        'order' => 1,
    ]);

    $this->actingAs($admin)
        ->get(ChapterResource::getUrl('edit', ['record' => $child]))
        ->assertOk()
        ->assertSee('Vice City Test')
        ->assertSee('Ken Rosenberg Missions')
        ->assertSee('The Party')
        ->assertSee('Add Sidebar Page')
        ->assertSeeInOrder(['Sidebar Page', 'Walkthrough Document'])
        ->assertDontSee('Game Settings & Chapters')
        ->assertDontSee('Foreign Chapter');
});

test('admin adds a sidebar page without manually writing a slug', function () {
    $this->seed(RoleSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole('super_admin');
    $game = Game::create([
        'title' => 'Simple Admin Game',
        'slug' => 'simple-admin-game',
        'route_slug' => 'simple-admin-game',
        'is_published' => true,
    ]);
    Chapter::create([
        'game_id' => $game->id,
        'chapter_title' => 'Existing Route',
        'slug' => 'existing-route',
        'section_title' => 'Main Walkthrough',
        'order' => 1,
    ]);

    Livewire::actingAs($admin)
        ->test(CreateChapter::class)
        ->fillForm([
            'game_id' => $game->id,
            'chapter_title' => 'Church of Elleh',
            'order' => 2,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('chapters', [
        'game_id' => $game->id,
        'chapter_title' => 'Church of Elleh',
        'slug' => 'church-of-elleh',
        'section_title' => 'Main Walkthrough',
    ]);
});

test('admin edits a chapter as one word style walkthrough document', function () {
    $this->seed(RoleSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole('super_admin');

    $game = Game::create([
        'title' => 'Document Game',
        'slug' => 'document-game',
        'route_slug' => 'document-game',
        'is_published' => true,
    ]);
    $chapter = Chapter::create([
        'game_id' => $game->id,
        'chapter_title' => 'Stormveil Castle',
        'slug' => 'stormveil-castle',
        'order' => 1,
    ]);
    $chapter->steps()->createMany([
        [
            'step_title' => 'Masuk Stormveil',
            'content' => '<p>Lewati gerbang utama.</p>',
            'order' => 1,
        ],
        [
            'step_title' => 'Hadapi Godrick',
            'content' => '<p>Gunakan Spirit Ash.</p>',
            'order' => 2,
        ],
    ]);

    Livewire::actingAs($admin)
        ->test(EditChapter::class, ['record' => $chapter->getRouteKey()])
        ->assertFormSet([
            'document_content' => fn (string $content): bool => str_contains($content, '<h2>Masuk Stormveil</h2>')
                && str_contains($content, '<h2>Hadapi Godrick</h2>'),
        ])
        ->fillForm([
            'document_content' => '<h2 style="font-size:80px">Rute Lengkap</h2><p>Masuk melalui <a href="https://example.com">gerbang samping</a>.</p><ul><li>Ambil Site of Grace.</li></ul><figure><img src="https://images.example.com/stormveil.jpg" style="width:2000px" onerror="alert(1)"><figcaption>Add a caption...</figcaption></figure>',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($chapter->steps()->count())->toBe(1);

    $documentStep = $chapter->steps()->firstOrFail();

    expect($documentStep->step_title)->toBe('Stormveil Castle')
        ->and($documentStep->content)->toContain('<h2>Rute Lengkap</h2>')
        ->and($documentStep->content)->toContain('<mark>gerbang samping</mark>')
        ->and($documentStep->content)->toContain('Ambil Site of Grace.')
        ->and($documentStep->content)->toContain('<img src="https://images.example.com/stormveil.jpg" loading="lazy" decoding="async">')
        ->and($documentStep->content)->not->toContain('<a ')
        ->and($documentStep->content)->not->toContain('caption')
        ->and($documentStep->content)->not->toContain('style=')
        ->and($documentStep->content)->not->toContain('onerror=');

    $this->get(route('games.walkthrough.show', [
        'gameSlug' => $game->route_slug,
        'chapterSlug' => $chapter->slug,
    ]))
        ->assertOk()
        ->assertSee('<h2>Rute Lengkap</h2>', false)
        ->assertDontSee('<h2>Stormveil Castle</h2>', false);
});

test('walkthrough paste sanitizer normalizes oversized headings and bold paragraphs', function () {
    $longText = str_repeat('Paragraf panjang dari halaman referensi. ', 8);
    $html = '<h2 style="font-size:80px">' . $longText . '</h2>'
        . '<p><strong>' . $longText . '</strong></p>'
        . '<p><a href="https://example.com">Golden Seed</a></p>'
        . '<figure><img data-src="//images.example.com/route.jpg"><figcaption>Add a caption...</figcaption></figure>'
        . '<figure><img src="data:image/gif;base64,abc" data-srcset="//images.example.com/lazy-route.jpg 1x, //images.example.com/lazy-route@2x.jpg 2x"></figure>';

    $sanitized = \App\Support\RichText::sanitizeWalkthrough($html);

    expect($sanitized)
        ->toContain('<p>' . $longText . '</p>')
        ->toContain('<mark>Golden Seed</mark>')
        ->toContain('<img src="https://images.example.com/route.jpg" loading="lazy" decoding="async">')
        ->toContain('<img src="https://images.example.com/lazy-route.jpg" loading="lazy" decoding="async">')
        ->not->toContain('<h2>')
        ->not->toContain('<strong>')
        ->not->toContain('caption')
        ->not->toContain('style=')
        ->not->toContain('href=');
});

test('walkthrough detail uses game theme preset from database', function () {
    $game = Game::create([
        'title' => 'Theme Game',
        'slug' => 'theme-game',
        'route_slug' => 'theme-game',
        'theme_preset' => 'gold',
        'is_published' => true,
    ]);
    $chapter = $game->chapters()->create([
        'chapter_title' => 'Gold Route',
        'slug' => 'gold-route',
        'order' => 1,
    ]);
    $chapter->steps()->create([
        'step_title' => 'Gold Route',
        'content' => '<p>Ikuti rute utama.</p>',
        'order' => 1,
    ]);

    $this->get(route('games.walkthrough.show', [
        'gameSlug' => $game->route_slug,
        'chapterSlug' => $chapter->slug,
    ]))
        ->assertOk()
        ->assertSee('body class="theme-gold game-theme-game"', false)
        ->assertSee('--guide-accent: #d9b45b', false)
        ->assertSee('class="back-to-top"', false);
});
