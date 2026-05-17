<?php

namespace Database\Seeders;

use App\Models\Review;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $reviews = [
            [
                'game_title' => 'Horizon Forbidden West',
                'guide_type' => 'Story Walkthrough',
                'title' => 'Horizon: Finish Main Story Fast',
                'excerpt' => 'Langsung fokus ke alur utama dan misi penting tanpa melewatkan boss utama.',
                'views' => 2450,
                'rating' => 89,
            ],
            [
                'game_title' => 'The Last of Us Part II',
                'guide_type' => 'Boss & Ending Guide',
                'title' => 'Tamat Tanpa Tersesat',
                'excerpt' => 'Panduan cepat untuk menuntaskan cerita utama dan akhir terbaik dengan langkah jelas.',
                'views' => 1810,
                'rating' => 92,
            ],
            [
                'game_title' => 'God of War Ragnarok',
                'guide_type' => 'Story Route',
                'title' => 'Ragnarok: Route Kilat',
                'excerpt' => 'Selesaikan semua quest utama dan boss dengan efisien tanpa kehilangan narasi penting.',
                'views' => 2175,
                'rating' => 94,
            ],
            [
                'game_title' => 'Elden Ring',
                'guide_type' => 'Main Quest Walkthrough',
                'title' => 'Elden Ring: Road to Endgame',
                'excerpt' => 'Rute utama untuk sampai ke final boss dengan tip barang penting dan kejatuhan musuh.',
                'views' => 2950,
                'rating' => 91,
            ],
            [
                'game_title' => 'Sekiro: Shadows Die Twice',
                'guide_type' => 'Boss Strategy',
                'title' => 'Sekiro: Boss Perfect Guide',
                'excerpt' => 'Strategi cepat untuk mengalahkan boss paling sulit tanpa perlu grind terlalu lama.',
                'views' => 1425,
                'rating' => 87,
            ],
        ];

        foreach ($reviews as $review) {
            Review::create($review);
        }
    }
}
