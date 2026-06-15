<?php

use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ContributionController;
use App\Http\Controllers\ContributionStepController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PublicContributionController;
use App\Http\Controllers\WalkthroughController;
use App\Models\Game;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

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
Route::get('/', [CatalogController::class, 'index'])->name('home');

Route::get('/videos', function () {
    return view('videos');
})->name('videos.index');

Route::get('/walkthrough', [WalkthroughController::class, 'showGame'])->name('walkthrough.game');
Route::get('/walkthrough/{slug}', [WalkthroughController::class, 'showChapter'])->name('walkthrough.chapter');

Route::get('/cover/{slug}', function ($slug) {
    $game = Game::where('route_slug', $slug)
        ->where('is_published', true)
        ->firstOrFail();

    if (str_starts_with($game->cover_image ?? '', 'http')) {
        return redirect()->away($game->cover_image);
    }

    $file = public_path($game->cover_image ?? '');

    if ($game->cover_image && is_file($file)) {
        return response()->file($file);
    }

    abort_unless(
        $game->cover_image && Storage::disk('public')->exists($game->cover_image),
        404,
    );

    return Storage::disk('public')->response($game->cover_image);
})->name('cover');

Route::get('/games/persona-3/story/{mission}', [WalkthroughController::class, 'showMission'])
    ->name('persona.story.show');

Route::get('/games/{gameSlug}/walkthrough/{chapterSlug}', [WalkthroughController::class, 'showGameChapter'])
    ->name('games.walkthrough.show');

Route::get('/community-guides/{contribution}', [PublicContributionController::class, 'show'])
    ->name('contributions.show');

Route::get('/games/{slug}', [CatalogController::class, 'show'])->name('games.show');

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
        $user->assignRole(Role::firstOrCreate([
            'name' => 'member',
            'guard_name' => 'web',
        ]));

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route('home');
    })->middleware('throttle:6,1')->name('register.store');

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

        if (Auth::user()->hasRole('super_admin')) {
            return redirect('/admin');
        }

        return redirect()->intended(route('home'));
    })->middleware('throttle:6,1')->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/dashboard/walkthroughs', [ContributionController::class, 'index'])
        ->name('contributions.index');
    Route::get('/dashboard/walkthroughs/create', [ContributionController::class, 'create'])
        ->name('contributions.create');
    Route::post('/dashboard/walkthroughs', [ContributionController::class, 'store'])
        ->name('contributions.store');
    Route::get('/dashboard/walkthroughs/{contribution}/edit', [ContributionController::class, 'edit'])
        ->name('contributions.edit');
    Route::put('/dashboard/walkthroughs/{contribution}', [ContributionController::class, 'update'])
        ->name('contributions.update');
    Route::delete('/dashboard/walkthroughs/{contribution}', [ContributionController::class, 'destroy'])
        ->name('contributions.destroy');
    Route::post('/dashboard/walkthroughs/{contribution}/submit', [ContributionController::class, 'submit'])
        ->name('contributions.submit');

    Route::post('/dashboard/walkthroughs/{contribution}/steps', [ContributionStepController::class, 'store'])
        ->name('contribution-steps.store');
    Route::get('/dashboard/walkthrough-steps/{step}/edit', [ContributionStepController::class, 'edit'])
        ->name('contribution-steps.edit');
    Route::put('/dashboard/walkthrough-steps/{step}', [ContributionStepController::class, 'update'])
        ->name('contribution-steps.update');
    Route::delete('/dashboard/walkthrough-steps/{step}', [ContributionStepController::class, 'destroy'])
        ->name('contribution-steps.destroy');

    Route::post('/logout', function (Request $request) {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    })->name('logout');
});
