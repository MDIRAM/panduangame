<?php

namespace Database\Seeders;

use App\Models\Chapter;
use App\Models\Game;
use App\Models\Step;
use App\Services\PersonaCsvImporter;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    public function run(): void
    {
        $game = Game::updateOrCreate(
            ['slug' => 'persona-3-reload'],
            [
                'title' => 'Persona 3 Reload',
                'description' => 'Story mission walkthrough, battle guide, dan jadwal progres Persona 3 Reload.',
                'cover_image' => 'coverimg/Persona_3.webp',
            ],
        );

        $chapters = $this->seedChapterNavigation($game);

        $this->removeEmptyLegacyChapters($game);
        $this->seedPrologue($chapters['prologue-april-7-april-18']);
        $this->localizePrologue($chapters['prologue-april-7-april-18']);
        $this->importAvailableCsvFiles($chapters);
        $this->seedTartarusImages($chapters['first-visit-to-tartarus-april-19-april-20']);
        $this->localizeTartarus($chapters['first-visit-to-tartarus-april-19-april-20']);
        $this->seedMayGuide($chapters['full-moon-operation-may']);
        $this->seedJuneGuide($chapters['full-moon-operation-june']);
        $this->seedTheurgyFieldTest($chapters['theurgy-field-test-june-13']);
        $this->seedJulyGuide($chapters['full-moon-operation-july']);
        $this->seedSummerVacation($chapters['summer-vacation-july-20-july-23']);
        $this->seedAugustGuide($chapters['full-moon-operation-august']);
        $this->seedShadowOfTheAbyss($chapters['shadow-of-the-abyss-story-event-august-14']);
        $this->seedSeptemberGuide($chapters['full-moon-operation-september']);
        $this->seedOctoberGuide($chapters['full-moon-operation-october']);
        $this->seedNovemberGuide($chapters['full-moon-operation-november']);
        $this->seedSchoolTrip($chapters['school-trip-november-17-november-20']);
        $this->seedChidoriBattle($chapters['chidori-battle-november-22']);
        $this->seedFinalMission($chapters['final-mission-the-promised-day-january-31']);
    }

    private function seedChapterNavigation(Game $game): array
    {
        $groups = json_decode(
            file_get_contents(resource_path('data/persona3/missions.json')),
            true,
            flags: JSON_THROW_ON_ERROR,
        );

        $chapters = [];
        $order = 1;

        foreach ($groups as $sectionTitle => $missions) {
            foreach ($missions as $mission) {
                $chapter = Chapter::updateOrCreate(
                    [
                        'game_id' => $game->id,
                        'slug' => $mission['slug'],
                    ],
                    [
                        'chapter_title' => $mission['title'],
                        'section_title' => $sectionTitle,
                        'order' => $order,
                    ],
                );

                $chapters[$chapter->slug] = $chapter;
                $order++;
            }
        }

        return $chapters;
    }

    private function removeEmptyLegacyChapters(Game $game): void
    {
        Chapter::query()
            ->where('game_id', $game->id)
            ->whereIn('slug', ['april-walkthrough', 'may-walkthrough', 'june-walkthrough'])
            ->doesntHave('steps')
            ->delete();
    }

    private function seedPrologue(Chapter $chapter): void
    {
        if ($chapter->steps()->exists()) {
            return;
        }

        $sections = json_decode(
            file_get_contents(resource_path('data/persona3/prologue.json')),
            true,
            flags: JSON_THROW_ON_ERROR,
        );

        $heading = 'Prologue';
        $order = 1;
        $lastStep = null;

        foreach ($sections as $section) {
            if ($section['type'] === 'heading') {
                $heading = strip_tags($section['body']);

                continue;
            }

            if ($section['type'] === 'paragraph') {
                $lastStep = Step::create([
                    'chapter_id' => $chapter->id,
                    'step_title' => $heading,
                    'content' => trim(strip_tags($section['body'])),
                    'image_url' => null,
                    'order' => $order,
                ]);

                $order++;

                continue;
            }

            if ($section['type'] === 'image' && $lastStep && ! $lastStep->image_url) {
                $lastStep->update([
                    'image_url' => 'coverimg/Persona3/'.$section['image'],
                ]);
            }
        }
    }

    private function localizePrologue(Chapter $chapter): void
    {
        $translations = json_decode(
            file_get_contents(resource_path('data/persona3/prologue-id.json')),
            true,
            flags: JSON_THROW_ON_ERROR,
        );

        foreach ($translations as $translation) {
            $chapter->steps()
                ->where('order', $translation['order'])
                ->update([
                    'step_title' => $translation['title'],
                    'content' => $translation['content'],
                ]);
        }
    }

    private function importAvailableCsvFiles(array $chapters): void
    {
        $importer = app(PersonaCsvImporter::class);

        foreach ($chapters as $slug => $chapter) {
            $paths = [
                base_path('database/seeders/persona3/'.$slug.'.csv'),
            ];

            if ($slug === 'first-visit-to-tartarus-april-19-april-20') {
                $paths[] = base_path('database/seeders/tartarus.csv');
            }

            foreach ($paths as $path) {
                if (! is_file($path)) {
                    continue;
                }

                $importer->import($chapter, $path);

                break;
            }
        }
    }

    private function seedTartarusImages(Chapter $chapter): void
    {
        $images = [
            1 => 'https://oyster.ignimgs.com/mediawiki/apis.ign.com/persona-3-reload/0/0c/Persona_3_Reload_20240119000900.jpg',
            3 => 'https://oyster.ignimgs.com/mediawiki/apis.ign.com/persona-3-reload/7/7b/Persona_3_Reload_20240118235019.jpg',
            5 => 'https://oyster.ignimgs.com/mediawiki/apis.ign.com/persona-3-reload/f/f8/Persona_3_Reload_20240119000632.jpg',
            6 => 'https://oyster.ignimgs.com/mediawiki/apis.ign.com/persona-3-reload/8/8b/Persona_3_Reload_20240119001310.jpg',
            7 => 'https://oyster.ignimgs.com/mediawiki/apis.ign.com/persona-3-reload/2/2b/Persona_3_Reload_20240119001445.jpg',
            9 => 'https://oyster.ignimgs.com/mediawiki/apis.ign.com/persona-3-reload/d/d6/Persona_3_Reload_20240119144941.jpg',
            10 => 'https://oyster.ignimgs.com/mediawiki/apis.ign.com/persona-3-reload/b/b3/Persona_3_Reload_20240120153354.jpg',
            11 => 'https://oyster.ignimgs.com/mediawiki/apis.ign.com/persona-3-reload/3/30/Persona_3_Reload_20240120153941.jpg',
            13 => 'https://oyster.ignimgs.com/mediawiki/apis.ign.com/persona-3-reload/f/f2/Persona_3_Reload_20240119114754.jpg',
            14 => 'https://oyster.ignimgs.com/mediawiki/apis.ign.com/persona-3-reload/8/8a/Persona_3_Reload_20240119114752.jpg',
            15 => 'https://oyster.ignimgs.com/mediawiki/apis.ign.com/persona-3-reload/8/83/Persona_3_Reload_20240119114949.jpg',
            16 => 'https://oyster.ignimgs.com/mediawiki/apis.ign.com/persona-3-reload/0/03/Persona_3_Reload_20240119115558.jpg',
            17 => 'https://oyster.ignimgs.com/mediawiki/apis.ign.com/persona-3-reload/a/a7/Persona_3_Reload_20240119120538.jpg',
            18 => 'https://oyster.ignimgs.com/mediawiki/apis.ign.com/persona-3-reload/7/79/Persona_3_Reload_20240119120103.jpg',
            19 => 'https://oyster.ignimgs.com/mediawiki/apis.ign.com/persona-3-reload/b/be/Persona_3_Reload_20240119143321.jpg',
            20 => 'https://oyster.ignimgs.com/mediawiki/apis.ign.com/persona-3-reload/8/86/Persona_3_Reload_20240119120636.jpg',
            21 => 'https://oyster.ignimgs.com/mediawiki/apis.ign.com/persona-3-reload/9/98/Persona_3_Reload_20240119120859.jpg',
            22 => 'https://oyster.ignimgs.com/mediawiki/apis.ign.com/persona-3-reload/6/66/Persona_3_Reload_20240119121243.jpg',
            23 => 'https://oyster.ignimgs.com/mediawiki/apis.ign.com/persona-3-reload/b/b6/Persona_3_Reload_20240119121318.jpg',
            24 => 'https://oyster.ignimgs.com/mediawiki/apis.ign.com/persona-3-reload/7/75/Persona_3_Reload_20240119121755.jpg',
            25 => 'https://oyster.ignimgs.com/mediawiki/apis.ign.com/persona-3-reload/4/42/Persona_3_Reload_20240119122745.jpg',
            26 => 'https://oyster.ignimgs.com/mediawiki/apis.ign.com/persona-3-reload/6/6f/Persona_3_Reload_20240119122312.jpg',
            27 => 'https://oyster.ignimgs.com/mediawiki/apis.ign.com/persona-3-reload/6/67/Persona_3_Reload_20240119122904.jpg',
            28 => 'https://oyster.ignimgs.com/mediawiki/apis.ign.com/persona-3-reload/6/68/Persona_3_Reload_20240119000639.jpg',
        ];

        foreach ($images as $order => $imageUrl) {
            $chapter->steps()
                ->where('order', $order)
                ->update(['image_url' => $imageUrl]);
        }
    }

    private function localizeTartarus(Chapter $chapter): void
    {
        $translations = json_decode(
            file_get_contents(resource_path('data/persona3/tartarus-id.json')),
            true,
            flags: JSON_THROW_ON_ERROR,
        );

        foreach ($translations as $translation) {
            $chapter->steps()
                ->where('order', $translation['order'])
                ->update([
                    'step_title' => $translation['title'],
                    'content' => $translation['content'],
                ]);
        }
    }

    private function seedMayGuide(Chapter $chapter): void
    {
        $chapter->update([
            'source_url' => 'https://www.ign.com/wikis/persona-3-reload/Full_Moon_Operation_-_May_Walkthrough',
        ]);

        if ($chapter->steps()->exists()) {
            return;
        }

        $steps = [
            [
                'title' => 'Mulai Operasi Full Moon',
                'content' => 'Pada 9 Mei, operasi dimulai otomatis setelah sekolah. Tim SEES mendeteksi Shadow di luar Tartarus dan mengirim Makoto, Yukari, serta Junpei menuju Stasiun Iwatodai.',
                'image' => 'https://oyster.ignimgs.com/mediawiki/apis.ign.com/persona-3-reload/2/2b/Persona_3_Reload_20240121112648.jpg',
            ],
            [
                'title' => 'Masuk ke Monorail',
                'content' => 'Seberangi jalur rel hingga mencapai kereta monorail. Setelah cutscene selesai, bergerak maju melewati gerbong dan periksa jalur di depan.',
                'image' => 'https://oyster.ignimgs.com/mediawiki/apis.ign.com/persona-3-reload/f/f6/Persona_3_Reload_20240121112944.jpg',
            ],
            [
                'title' => 'Kejar Junpei',
                'content' => 'Di antara gerbong 8 dan 9, sebuah Shadow muncul dan Junpei mengejarnya. Makoto dan Yukari harus menyusul sambil menghadapi musuh di sepanjang kereta.',
                'image' => 'https://oyster.ignimgs.com/mediawiki/apis.ign.com/persona-3-reload/f/fe/Persona_3_Reload_20240121113157.jpg',
            ],
            [
                'title' => 'Lawan Spurious Book',
                'content' => 'Dua Spurious Book memakai serangan Light. Hindari Persona yang lemah terhadap Light, serang cepat, dan jaga HP Makoto serta Yukari.',
                'image' => 'https://oyster.ignimgs.com/mediawiki/apis.ign.com/persona-3-reload/0/01/Persona_3_Reload_20240121114755.jpg',
            ],
            [
                'title' => 'Hadapi Gelombang Berikutnya',
                'content' => 'Setelah pertarungan pertama, tim kembali disergap oleh Spurious Book dan Heat Balance. Gunakan Garu milik Yukari untuk membantu menjatuhkan Heat Balance.',
                'image' => 'https://oyster.ignimgs.com/mediawiki/apis.ign.com/persona-3-reload/d/dd/Persona_3_Reload_20240121114604.jpg',
            ],
            [
                'title' => 'Pulihkan Party',
                'content' => 'Gunakan Auto Recover setelah area aman. Lanjutkan menuju gerbong berikutnya dan ambil item yang terlihat sebelum memicu pertarungan baru.',
                'image' => 'https://oyster.ignimgs.com/mediawiki/apis.ign.com/persona-3-reload/b/b0/Persona_3_Reload_20240121114813.jpg',
            ],
            [
                'title' => 'Junpei Bergabung Kembali',
                'content' => 'Junpei kembali ke party pada encounter berikutnya. Fokuskan serangan pada kelemahan tiap musuh agar party memperoleh kesempatan All-Out Attack.',
                'image' => 'https://oyster.ignimgs.com/mediawiki/apis.ign.com/persona-3-reload/1/11/Persona_3_Reload_20240121114005.jpg',
            ],
            [
                'title' => 'Kereta Mulai Bergerak',
                'content' => 'Sesudah musuh dikalahkan, monorail bergerak tanpa kendali dan gelombang Shadow lain muncul. Habisi mereka dengan cepat karena situasi mulai dibatasi waktu.',
                'image' => 'https://oyster.ignimgs.com/mediawiki/apis.ign.com/persona-3-reload/7/71/Persona_3_Reload_20240121114858.jpg',
            ],
            [
                'title' => 'Menuju Gerbong Depan',
                'content' => 'Bergegaslah menuju bagian depan kereta. Sebelum memasuki pintu berkaca buram, pulihkan seluruh party dan siapkan Persona Makoto untuk pertarungan boss.',
                'image' => 'https://oyster.ignimgs.com/mediawiki/apis.ign.com/persona-3-reload/8/8c/Persona_3_Reload_20240121115031.jpg',
            ],
            [
                'title' => 'Priestess Boss Fight',
                'content' => 'Masuk ke gerbong terakhir untuk menghadapi Priestess, Shadow yang mengendalikan monorail. Pertarungan harus diselesaikan sebelum kereta mencapai titik tabrakan.',
                'image' => 'https://oyster.ignimgs.com/mediawiki/apis.ign.com/persona-3-reload/6/6d/Persona_3_Reload_20240121115224.jpg',
            ],
            [
                'title' => 'Perhatikan Timer',
                'content' => 'Priestess dapat mempercepat kereta dan mengurangi waktu yang tersisa. Tentukan perintah dengan cepat dan hindari membuang giliran untuk aksi yang tidak penting.',
                'image' => 'https://oyster.ignimgs.com/mediawiki/apis.ign.com/persona-3-reload/f/f8/Persona_3_Reload_20240121115057.jpg',
            ],
            [
                'title' => 'Strategi Menyerang Priestess',
                'content' => 'Priestess tidak memiliki weakness dan memantulkan serangan Ice. Gunakan serangan fisik atau elemen selain Ice, lalu prioritaskan damage stabil dari Makoto dan Junpei.',
                'image' => 'https://oyster.ignimgs.com/mediawiki/apis.ign.com/persona-3-reload/0/04/Persona_3_Reload_20240121115157.jpg',
            ],
            [
                'title' => 'Bertahan dari Serangan Ice',
                'content' => 'Boss dapat menyerang seluruh party dengan sihir Ice. Jaga HP Yukari agar ia tetap bisa melakukan healing dan jangan gunakan Persona Makoto yang lemah terhadap Ice.',
                'image' => 'https://oyster.ignimgs.com/mediawiki/apis.ign.com/persona-3-reload/f/fb/Persona_3_Reload_20240121115343.jpg',
            ],
            [
                'title' => 'Perkuat Junpei',
                'content' => 'Jika Makoto memiliki Tarukaja, gunakan pada Junpei untuk meningkatkan damage fisiknya. Makoto dan Junpei menjadi sumber damage utama selama Yukari menjaga kondisi party.',
                'image' => 'https://oyster.ignimgs.com/mediawiki/apis.ign.com/persona-3-reload/3/35/Persona_3_Reload_20240121115417.jpg',
            ],
            [
                'title' => 'Kalahkan Despairing Tiara',
                'content' => 'Di tengah pertarungan, Priestess memanggil dua Despairing Tiara. Kalahkan kedua summon lebih dahulu agar tekanan ke party berkurang, kemudian kembali fokus ke boss.',
                'image' => 'https://oyster.ignimgs.com/mediawiki/apis.ign.com/persona-3-reload/b/b4/Persona_3_Reload_20240121120547.jpg',
            ],
            [
                'title' => 'Selesaikan Sebelum Waktu Habis',
                'content' => 'Jangan terlalu lama membuka menu karena timer terus menjadi ancaman. Pertahankan pola buff, damage, dan healing sampai HP Priestess habis untuk menyelesaikan operasi May.',
                'image' => null,
            ],
        ];

        foreach ($steps as $index => $step) {
            Step::create([
                'chapter_id' => $chapter->id,
                'step_title' => $step['title'],
                'content' => $step['content'],
                'image_url' => $step['image'],
                'order' => $index + 1,
            ]);
        }
    }

    private function seedJuneGuide(Chapter $chapter): void
    {
        $chapter->update([
            'source_url' => 'https://www.ign.com/wikis/persona-3-reload/Full_Moon_Operation_-_June_Walkthrough',
        ]);

        if ($chapter->steps()->exists()) {
            return;
        }

        $steps = json_decode(
            file_get_contents(resource_path('data/persona3/june-id.json')),
            true,
            flags: JSON_THROW_ON_ERROR,
        );

        foreach ($steps as $step) {
            Step::create([
                'chapter_id' => $chapter->id,
                'step_title' => $step['title'],
                'content' => $step['content'],
                'image_url' => $step['image_url'],
                'order' => $step['order'],
            ]);
        }
    }

    private function seedTheurgyFieldTest(Chapter $chapter): void
    {
        $chapter->update([
            'source_url' => 'https://www.ign.com/wikis/persona-3-reload/Theurgy_Field_Test_(June_13)_Walkthrough',
        ]);

        if ($chapter->steps()->exists()) {
            return;
        }

        $steps = json_decode(
            file_get_contents(resource_path('data/persona3/theurgy-field-test-id.json')),
            true,
            flags: JSON_THROW_ON_ERROR,
        );

        foreach ($steps as $step) {
            Step::create([
                'chapter_id' => $chapter->id,
                'step_title' => $step['title'],
                'content' => $step['content'],
                'image_url' => $step['image_url'],
                'order' => $step['order'],
            ]);
        }
    }

    private function seedJulyGuide(Chapter $chapter): void
    {
        $chapter->update([
            'source_url' => 'https://www.ign.com/wikis/persona-3-reload/Full_Moon_Operation_-_July_Walkthrough',
        ]);

        if ($chapter->steps()->exists()) {
            return;
        }

        $steps = json_decode(
            file_get_contents(resource_path('data/persona3/july-id.json')),
            true,
            flags: JSON_THROW_ON_ERROR,
        );

        foreach ($steps as $step) {
            Step::create([
                'chapter_id' => $chapter->id,
                'step_title' => $step['title'],
                'content' => $step['content'],
                'image_url' => $step['image_url'],
                'order' => $step['order'],
            ]);
        }
    }

    private function seedSummerVacation(Chapter $chapter): void
    {
        $chapter->update([
            'source_url' => 'https://www.ign.com/wikis/persona-3-reload/Summer_Vacation_(July_20_-_July_23)_Walkthrough',
        ]);

        if ($chapter->steps()->exists()) {
            return;
        }

        $steps = json_decode(
            file_get_contents(resource_path('data/persona3/summer-vacation-id.json')),
            true,
            flags: JSON_THROW_ON_ERROR,
        );

        foreach ($steps as $step) {
            Step::create([
                'chapter_id' => $chapter->id,
                'step_title' => $step['title'],
                'content' => $step['content'],
                'image_url' => $step['image_url'] ?? null,
                'order' => $step['order'],
            ]);
        }
    }

    private function seedAugustGuide(Chapter $chapter): void
    {
        $chapter->update([
            'source_url' => 'https://www.ign.com/wikis/persona-3-reload/Full_Moon_Operation_-_August_Walkthrough',
        ]);

        if ($chapter->steps()->exists()) {
            return;
        }

        $steps = json_decode(
            file_get_contents(resource_path('data/persona3/august-id.json')),
            true,
            flags: JSON_THROW_ON_ERROR,
        );

        foreach ($steps as $step) {
            Step::create([
                'chapter_id' => $chapter->id,
                'step_title' => $step['title'],
                'content' => $step['content'],
                'image_url' => $step['image_url'] ?? null,
                'order' => $step['order'],
            ]);
        }
    }

    private function seedShadowOfTheAbyss(Chapter $chapter): void
    {
        $chapter->update([
            'source_url' => 'https://www.ign.com/wikis/persona-3-reload/Shadow_of_the_Abyss_Story_Event_(August_14)_Walkthrough',
        ]);

        if ($chapter->steps()->exists()) {
            return;
        }

        $steps = json_decode(
            file_get_contents(resource_path('data/persona3/shadow-of-the-abyss-id.json')),
            true,
            flags: JSON_THROW_ON_ERROR,
        );

        foreach ($steps as $step) {
            Step::create([
                'chapter_id' => $chapter->id,
                'step_title' => $step['title'],
                'content' => $step['content'],
                'image_url' => $step['image_url'] ?? null,
                'order' => $step['order'],
            ]);
        }
    }

    private function seedSeptemberGuide(Chapter $chapter): void
    {
        $chapter->update([
            'source_url' => 'https://www.ign.com/wikis/persona-3-reload/Full_Moon_Operation_-_September_Walkthrough',
        ]);

        if ($chapter->steps()->exists()) {
            return;
        }

        $steps = json_decode(
            file_get_contents(resource_path('data/persona3/september-id.json')),
            true,
            flags: JSON_THROW_ON_ERROR,
        );

        foreach ($steps as $step) {
            Step::create([
                'chapter_id' => $chapter->id,
                'step_title' => $step['title'],
                'content' => $step['content'],
                'image_url' => $step['image_url'] ?? null,
                'order' => $step['order'],
            ]);
        }
    }

    private function seedOctoberGuide(Chapter $chapter): void
    {
        $chapter->update([
            'source_url' => 'https://www.ign.com/wikis/persona-3-reload/Full_Moon_Operation_-_October_Walkthrough',
        ]);

        if ($chapter->steps()->exists()) {
            return;
        }

        $steps = json_decode(
            file_get_contents(resource_path('data/persona3/october-id.json')),
            true,
            flags: JSON_THROW_ON_ERROR,
        );

        foreach ($steps as $step) {
            Step::create([
                'chapter_id' => $chapter->id,
                'step_title' => $step['title'],
                'content' => $step['content'],
                'image_url' => $step['image_url'] ?? null,
                'order' => $step['order'],
            ]);
        }
    }

    private function seedNovemberGuide(Chapter $chapter): void
    {
        $chapter->update([
            'source_url' => 'https://www.ign.com/wikis/persona-3-reload/Full_Moon_Operation_-_November_Walkthrough',
        ]);

        if ($chapter->steps()->exists()) {
            return;
        }

        $steps = json_decode(
            file_get_contents(resource_path('data/persona3/november-id.json')),
            true,
            flags: JSON_THROW_ON_ERROR,
        );

        foreach ($steps as $step) {
            Step::create([
                'chapter_id' => $chapter->id,
                'step_title' => $step['title'],
                'content' => $step['content'],
                'image_url' => $step['image_url'] ?? null,
                'order' => $step['order'],
            ]);
        }
    }

    private function seedSchoolTrip(Chapter $chapter): void
    {
        $chapter->update([
            'source_url' => 'https://www.ign.com/wikis/persona-3-reload/School_Trip_(November_17)_Walkthrough',
        ]);

        if ($chapter->steps()->exists()) {
            return;
        }

        $steps = json_decode(
            file_get_contents(resource_path('data/persona3/school-trip-id.json')),
            true,
            flags: JSON_THROW_ON_ERROR,
        );

        foreach ($steps as $step) {
            Step::create([
                'chapter_id' => $chapter->id,
                'step_title' => $step['title'],
                'content' => $step['content'],
                'image_url' => $step['image_url'] ?? null,
                'order' => $step['order'],
            ]);
        }
    }

    private function seedChidoriBattle(Chapter $chapter): void
    {
        $chapter->update([
            'source_url' => 'https://www.ign.com/wikis/persona-3-reload/Chidori_Battle_(November_22)_Walkthrough',
        ]);

        if ($chapter->steps()->exists()) {
            return;
        }

        $steps = json_decode(
            file_get_contents(resource_path('data/persona3/chidori-battle-id.json')),
            true,
            flags: JSON_THROW_ON_ERROR,
        );

        foreach ($steps as $step) {
            Step::create([
                'chapter_id' => $chapter->id,
                'step_title' => $step['title'],
                'content' => $step['content'],
                'image_url' => $step['image_url'] ?? null,
                'order' => $step['order'],
            ]);
        }
    }

    private function seedFinalMission(Chapter $chapter): void
    {
        $chapter->update([
            'source_url' => 'https://www.ign.com/wikis/persona-3-reload/Final_Mission:_The_Promised_Day_(January_31)',
        ]);

        if ($chapter->steps()->exists()) {
            return;
        }

        $steps = json_decode(
            file_get_contents(resource_path('data/persona3/final-mission-id.json')),
            true,
            flags: JSON_THROW_ON_ERROR,
        );

        foreach ($steps as $step) {
            Step::create([
                'chapter_id' => $chapter->id,
                'step_title' => $step['title'],
                'content' => $step['content'],
                'image_url' => $step['image_url'] ?? null,
                'order' => $step['order'],
            ]);
        }
    }
}
