<?php

use App\Http\Controllers\WalkthroughController;
use App\Models\Chapter;
use App\Models\Game;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

/* NOTE: Do Not Remove
/ Livewire asset handling if using sub folder in domain
*/

Livewire::setUpdateRoute(function ($handle) {
    return Route::post(config('app.asset_prefix') . '/livewire/update', $handle);
});

Livewire::setScriptRoute(function ($handle) {
    return Route::get(config('app.asset_prefix') . '/livewire/livewire.js', $handle);
});
/*
/ END
*/
Route::get('/', function () {
    return view('welcome');
});

Route::get('/videos', function () {
    return view('videos');
})->name('videos.index');

Route::get('/walkthrough', [WalkthroughController::class, 'showGame'])->name('walkthrough.game');
Route::get('/walkthrough/{slug}', [WalkthroughController::class, 'showChapter'])->name('walkthrough.chapter');

Route::get('/cover/{slug}', function ($slug) {
    $coverMap = [
        'elden-ring' => 'EldenRing.png',
        'dark-souls-2' => 'Dark_Souls_2.jpg',
        'persona-3' => 'Persona_3.webp',
    ];

    $candidateDirs = [
        public_path('coverimg/'),
        public_path('images/games/'),
    ];

    $candidates = [];

    if (array_key_exists($slug, $coverMap)) {
        foreach ($candidateDirs as $dir) {
            $candidates[] = $dir.$coverMap[$slug];
        }
    }

    $candidates[] = public_path('images/games/').$slug.'.jpg';
    $candidates[] = public_path('images/games/').$slug.'.png';
    $candidates[] = public_path('images/games/').$slug.'.svg';

    foreach ($candidates as $file) {
        if (file_exists($file)) {
            return response()->file($file);
        }
    }

    abort(404);
})->name('cover');

Route::get('/games/persona-3/story/{mission}', [WalkthroughController::class, 'showMission'])
    ->name('persona.story.show');

Route::get('/games/{gameSlug}/walkthrough/{chapterSlug}', [WalkthroughController::class, 'showGameChapter'])
    ->name('games.walkthrough.show');

Route::get('/games/{slug}', function ($slug) {
    $games = [
        'elden-ring' => [
            'title' => 'Elden Ring',
            'subtitle' => 'Panduan story dan boss di The Lands Between.',
            'description' => 'Rute lengkap untuk mencapai ending utama, termasuk strategi boss dan quest inti.',
            'highlights' => [
                'Jalan cerita utama hingga Queen Marika',
                'Strategi boss utama dan equipment terbaik',
                'Rute optional untuk menemukan ending tersembunyi',
            ],
        ],
        'dark-souls-2' => [
            'title' => 'Dark Souls 2',
            'subtitle' => 'Panduan story hingga akhir Drangleic.',
            'description' => 'Langkah demi langkah untuk menyelesaikan game dengan fokus pada inti cerita.',
            'highlights' => [
                'Rute menuju Majula dan Throne of Want',
                'Siapkan build yang efisien untuk boss utama',
                'Panduan lokasi item penting dan shortcut',
            ],
        ],
        'persona-3' => [
            'title' => 'Persona 3',
            'subtitle' => 'Walkthrough cerita utama dan Social Link yang penting.',
            'description' => 'Panduan untuk menyelesaikan story hingga True Ending dengan Social Link terpilih.',
            'highlights' => [
                'Panduan Social Link dan jadwal harian',
                'Strategi battle untuk Tartarus',
                'Urutan event penting hingga akhir cerita',
            ],
        ],
    ];

    abort_if(! array_key_exists($slug, $games), 404);

    $databaseSlug = match ($slug) {
        'persona-3' => 'persona-3-reload',
        default => $slug,
    };

    $databaseGame = Schema::hasTable('games')
        ? Game::query()
            ->where('slug', $databaseSlug)
            ->with([
                'chapters' => fn ($query) => $query->orderBy('order'),
                'chapters.steps' => fn ($query) => $query->orderBy('order'),
            ])
            ->first()
        : null;

    return view('games.show', [
        'game' => $games[$slug],
        'slug' => $slug,
        'databaseGame' => $databaseGame,
        'personaChapters' => $slug === 'persona-3'
            ? $databaseGame?->chapters ?? collect()
            : collect(),
    ]);
})->name('games.show');

Route::middleware('guest')->group(function () {
    Route::view('/login', 'auth.login')->name('login');
    Route::view('/register', 'auth.register')->name('register');

    Route::post('/register', function (Request $request) {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create($validated);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route('dashboard');
    })->name('register.store');

    Route::post('/login', function (Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Email atau password tidak cocok.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    })->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    Route::post('/logout', function (Request $request) {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    })->name('logout');
});
