<?php

use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Illuminate\Support\Facades\Response;

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

Route::get('/cover/{slug}', function ($slug) {
    $base = base_path('src/public/images/games/');
    $candidates = [
        $base.$slug.'.jpg',
        $base.$slug.'.png',
        $base.$slug.'.svg',
    ];

    foreach ($candidates as $file) {
        if (file_exists($file)) {
            return response()->file($file);
        }
    }

    abort(404);
})->name('cover');

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
        'dark-souls-1' => [
            'title' => 'Dark Souls 1',
            'subtitle' => 'Panduan penuh dari Firelink Shrine sampai Lord Souls.',
            'description' => 'Fokus pada rute story utama, bonfire penting, dan boss build untuk pemain baru.',
            'highlights' => [
                'Navigasi ke Lordvessel dan Lord Souls',
                'Battle tips untuk Ornstein & Smough',
                'Rute cepat ke ending tanpa terlalu banyak side quest',
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
        'dark-souls-3' => [
            'title' => 'Dark Souls 3',
            'subtitle' => 'Panduan ending andalan untuk Ashes of Ariandel dan The Ringed City.',
            'description' => 'Jalan cerita utama dengan strategi boss modern dan rute finish cepat.',
            'highlights' => [
                'Rute story ke Irithyll dan Lothric Castle',
                'Boss guide untuk setiap peiogn',
                'Saran equipment untuk menyelesaikan game dengan mudah',
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
        'persona-4' => [
            'title' => 'Persona 4',
            'subtitle' => 'Panduan tamat Misteri, Velvet Room, dan True Ending.',
            'description' => 'Fokus pada bagian story utama dan perkembangan karakter untuk hasil terbaik.',
            'highlights' => [
                'Route story hingga Mystery Case Closed',
                'Tips leveling Persona dan Social Link',
                'Checklist tugas penting setiap bulan',
            ],
        ],
    ];

    abort_if(! array_key_exists($slug, $games), 404);

    return view('games.show', ['game' => $games[$slug], 'slug' => $slug]);
})->name('games.show');

Route::view('/login', 'auth.login')->name('login');
Route::view('/register', 'auth.register')->name('register');
Route::view('/dashboard', 'dashboard')->name('dashboard');
