<?php

namespace Database\Seeders;

use App\Models\Chapter;
use App\Models\Game;
use App\Models\Step;
use Illuminate\Database\Seeder;

class EldenRingSeeder extends Seeder
{
    public function run(): void
    {
        $game = Game::updateOrCreate(
            ['slug' => 'elden-ring'],
            [
                'route_slug' => 'elden-ring',
                'title' => 'Elden Ring',
                'subtitle' => 'Panduan story dan boss di The Lands Between.',
                'description' => 'Rute awal dari Limgrave menuju kemenangan atas Godrick di Stormveil Castle.',
                'highlights' => [
                    'Rute upgrade dan equipment penting di Limgrave',
                    'Quest NPC awal tanpa melewatkan item utama',
                    'Strategi Margit dan Godrick untuk build pemula',
                ],
                'cover_image' => 'coverimg/EldenRing.png',
                'is_featured' => true,
                'is_published' => true,
            ],
        );

        $areas = json_decode(
            file_get_contents(resource_path('data/elden-ring/progress-route.json')),
            true,
            flags: JSON_THROW_ON_ERROR,
        );

        foreach ($areas as $index => $area) {
            $chapter = Chapter::updateOrCreate(
                [
                    'game_id' => $game->id,
                    'slug' => $area['slug'],
                ],
                [
                    'chapter_title' => $area['title'],
                    'section_title' => 'Main Walkthrough',
                    'overview' => $area['overview'],
                    'overview_image' => $area['overview_image'],
                    'cover_image' => $area['image'],
                    'source_url' => $area['source_url'],
                    'order' => $index + 1,
                ],
            );

            $chapter->steps()->delete();

            foreach ($area['steps'] as $stepIndex => $step) {
                Step::create([
                    'chapter_id' => $chapter->id,
                    'step_title' => $step['title'],
                    'content' => $step['content'],
                    'image_url' => $step['image_url'],
                    'order' => $stepIndex + 1,
                ]);
            }
        }
    }
}
