<?php

use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ChapterCommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GameFavoriteController;
use App\Http\Controllers\GameRatingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\WalkthroughController;
use App\Models\Game;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $memberRole = Role::firstOrCreate([
            'name' => 'member',
            'guard_name' => 'web',
        ]);
        $user->assignRole($memberRole);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home');
    })->name('register.store');

    // Forgot/Reset Password Routes
    Route::get('/forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');

    Route::post('/forgot-password', function (Request $request) {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    })->name('password.email');

    Route::get('/reset-password/{token}', function (string $token) {
        return view('auth.reset-password', ['token' => $token]);
    })->name('password.reset');

    Route::post('/reset-password', function (Request $request) {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    })->name('password.update');


    Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

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
    Route::post('/games/{game}/favorite', [GameFavoriteController::class, 'store'])
        ->name('games.favorite.store');
    Route::delete('/games/{game}/favorite', [GameFavoriteController::class, 'destroy'])
        ->name('games.favorite.destroy');
    Route::put('/games/{game}/rating', [GameRatingController::class, 'update'])
        ->name('games.rating.update');
    Route::delete('/games/{game}/rating', [GameRatingController::class, 'destroy'])
        ->name('games.rating.destroy');
    Route::post('/chapters/{chapter}/comments', [ChapterCommentController::class, 'store'])
        ->name('chapters.comments.store');
    Route::delete('/comments/{comment}', [ChapterCommentController::class, 'destroy'])
        ->name('comments.destroy');
    Route::put('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])
        ->name('profile.password.update');

    Route::post('/logout', function (Request $request) {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    })->name('logout');
});
