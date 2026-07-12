<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Exception $e) {
            return redirect()->route('login')->withErrors(['email' => 'Gagal login menggunakan Google. Silakan coba lagi.']);
        }

        // Cari user berdasarkan email
        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            // Jika user sudah ada, update google_id dan avatar_url (jika belum ada)
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar_url' => $user->avatar_url ?? $googleUser->getAvatar(),
            ]);
        } else {
            // Jika user belum ada, buat user baru
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar_url' => $googleUser->getAvatar(),
                'password' => null, // Password kosong karena login via Google
            ]);

            // Assign role 'member'
            $memberRole = Role::firstOrCreate([
                'name' => 'member',
                'guard_name' => 'web',
            ]);
            $user->assignRole($memberRole);
        }

        // Login-kan user
        Auth::login($user);

        // Regenerate session
        request()->session()->regenerate();

        // Redirect ke dashboard admin jika dia admin, atau ke home jika user biasa
        if ($user->hasRole('super_admin')) {
            return redirect('/admin');
        }

        return redirect()->intended(route('home'));
    }
}
