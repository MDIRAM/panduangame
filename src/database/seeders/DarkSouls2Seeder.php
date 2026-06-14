<?php

namespace Database\Seeders;

use App\Models\Chapter;
use App\Models\Game;
use App\Models\Step;
use Illuminate\Database\Seeder;

class DarkSouls2Seeder extends Seeder
{
    public function run(): void
    {
        $game = Game::updateOrCreate(
            ['slug' => 'dark-souls-2'],
            [
                'title' => 'Dark Souls 2',
                'description' => 'Game progress route menuju empat Great Souls dan akhir perjalanan di Drangleic.',
                'cover_image' => 'coverimg/Dark_Souls_2.jpg',
            ],
        );

        $areas = json_decode(
            file_get_contents(resource_path('data/dark-souls-2/progress-route.json')),
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
                    'section_title' => 'Game Progress Route',
                    'overview' => $area['overview'] ?? null,
                    'overview_image' => $area['overview_image'] ?? null,
                    'cover_image' => $area['image'] ?? null,
                    'source_url' => $area['source_url']
                        ?? 'https://darksouls2.wiki.fextralife.com/Game+Progress+Route',
                    'order' => $index + 1,
                ],
            );

            $chapter->steps()->delete();

            foreach ($area['steps'] as $stepIndex => $step) {
                $isStructuredStep = is_array($step);

                Step::create([
                    'chapter_id' => $chapter->id,
                    'step_title' => $isStructuredStep
                        ? $step['title']
                        : ($stepIndex === 0 ? $area['title'] : 'Langkah '.($stepIndex + 1)),
                    'content' => $isStructuredStep ? $step['content'] : $step,
                    'image_url' => $isStructuredStep
                        ? ($step['image_url'] ?? null)
                        : ($stepIndex === 0 ? $area['image'] : null),
                    'order' => $stepIndex + 1,
                ]);
            }
        }
    }
}
