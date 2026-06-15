<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\Game;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::where('email', config('app.admin_email'))->value('id');

        $reviews = [
            [
                'game_title' => 'Elden Ring',
                'guide_type' => 'Main Quest Walkthrough',
                'title' => 'Elden Ring: Road to Endgame',
                'excerpt' => 'Rute utama untuk sampai ke final boss dengan tip barang penting dan kejatuhan musuh.',
                'views' => 2950,
                'rating' => 91,
            ],
            [
                'game_title' => 'Dark Souls 2',
                'guide_type' => 'Game Progress Route',
                'title' => 'Dark Souls 2: Drangleic Route',
                'excerpt' => 'Urutan area, shortcut, dan boss utama menuju akhir perjalanan di Drangleic.',
                'views' => 2140,
                'rating' => 90,
            ],
            [
                'game_title' => 'Persona 3 Reload',
                'guide_type' => 'Story Mission Walkthrough',
                'title' => 'Persona 3 Reload: Story Route',
                'excerpt' => 'Panduan story mission dari April sampai pertarungan terakhir pada Januari.',
                'views' => 2380,
                'rating' => 93,
            ],
        ];

        Review::whereNull('game_id')->delete();

        foreach ($reviews as $review) {
            $gameId = Game::where('title', $review['game_title'])->value('id');

            Review::updateOrCreate(
                ['title' => $review['title']],
                [
                    ...$review,
                    'game_id' => $gameId,
                    'user_id' => $adminId,
                ],
            );
        }
    }
}
