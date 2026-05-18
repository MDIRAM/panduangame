<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
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

Route::get('/games/persona-3/story/{mission}', function ($mission) {
    $missions = [
        'prologue-april-7-april-18' => 'Prologue (April 7 - April 18) Walkthrough',
        'first-visit-to-tartarus-april-19-april-20' => 'First Visit to Tartarus (April 19 - April 20) Walkthrough',
        'full-moon-operation-may' => 'Full Moon Operation - May',
        'full-moon-operation-june' => 'Full Moon Operation - June',
        'theurgy-field-test-june-13' => 'Theurgy Field Test (June 13)',
        'full-moon-operation-july' => 'Full Moon Operation - July',
        'summer-vacation-july-20-july-23' => 'Summer Vacation (July 20 - July 23)',
        'full-moon-operation-august' => 'Full Moon Operation - August',
        'shadow-of-the-abyss-story-event-august-14' => 'Shadow of the Abyss Story Event (August 14)',
        'full-moon-operation-september' => 'Full Moon Operation - September',
        'full-moon-operation-october' => 'Full Moon Operation - October',
        'full-moon-operation-november' => 'Full Moon Operation - November',
        'school-trip-november-17-november-20' => 'School Trip (November 17 - November 20)',
        'chidori-battle-november-22' => 'Chidori Battle (November 22)',
        'final-mission-the-promised-day-january-31' => 'Final Mission: The Promised Day (January 31)',
    ];

    abort_if(! array_key_exists($mission, $missions), 404);

    return view('games.persona-story', [
        'mission' => $mission,
        'missionTitle' => $missions[$mission],
    ]);
})->name('persona.story.show');

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

    return view('games.show', ['game' => $games[$slug], 'slug' => $slug]);
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
