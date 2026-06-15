<?php

namespace Database\Seeders;

use App\Models\Chapter;
use App\Models\Game;
use App\Models\Step;
use Illuminate\Database\Seeder;

class GtaViceCitySeeder extends Seeder
{
    public function run(): void
    {
        $game = Game::updateOrCreate(
            ['slug' => 'gta-vice-city'],
            [
                'route_slug' => 'gta-vice-city',
                'title' => 'GTA Vice City',
                'subtitle' => 'Story mission route di Vice City.',
                'description' => 'Template walkthrough GTA Vice City untuk misi utama, dimulai dari In the Beginning dan An Old Friend.',
                'highlights' => [
                    'Sidebar mission dibuat sebagai template walkthrough',
                    'Contributor bisa memakai kerangka misi yang sudah tersedia',
                    'Isi awal diparafrasekan dari route pembuka Vice City',
                ],
                'cover_image' => 'coverimg/GTA_Vice_City.png',
                'is_featured' => false,
                'is_published' => true,
            ],
        );

        $chapters = [
            [
                'title' => 'In the Beginning... & An Old Friend',
                'slug' => 'in-the-beginning-and-an-old-friend',
                'overview' => [
                    'Pembuka GTA Vice City memperkenalkan Tommy Vercetti, kegagalan transaksi pertama di Vice City, dan kontak awal dengan Ken Rosenberg.',
                    'Bagian ini berfungsi sebagai tutorial awal: ambil kendaraan, pergi ke hotel, lalu hubungi Sonny Forelli untuk membuka rangkaian misi berikutnya.',
                ],
                'steps' => [
                    [
                        'title' => 'Tiba di Vice City',
                        'content' => 'Setelah cutscene pembuka, Tommy akan berada di jalan bersama Ken Rosenberg. Masuk ke mobil yang tersedia dan ikuti marker menuju Ocean View Hotel. Tidak perlu mencari senjata dulu; fokus saja mencapai safehouse pertama.',
                    ],
                    [
                        'title' => 'Menuju Ocean View Hotel',
                        'content' => 'Gunakan minimap untuk mengikuti rute ke hotel. Ini momen yang bagus untuk membiasakan diri dengan handling kendaraan, traffic, dan layout awal Vice City. Kalau menabrak sedikit tidak masalah, selama mobil masih bisa dipakai.',
                    ],
                    [
                        'title' => 'Simpan Progress',
                        'content' => 'Sesampainya di Ocean View Hotel, masuk ke marker untuk menyelesaikan bagian pembuka. Setelah itu, gunakan ikon kaset di safehouse untuk menyimpan progress sebelum lanjut ke objective berikutnya.',
                    ],
                    [
                        'title' => 'An Old Friend',
                        'content' => 'Masuk ke kamar hotel dan jawab telepon. Percakapan dengan Sonny menjelaskan masalah transaksi yang gagal dan menjadi alasan Tommy harus mulai membangun koneksi di Vice City.',
                    ],
                    [
                        'title' => 'Misi Berikutnya Terbuka',
                        'content' => 'Setelah panggilan selesai, misi awal berikutnya akan terbuka melalui kontak Ken Rosenberg. Dari sini, kamu bisa mulai mengikuti marker huruf di map untuk menjalankan story mission secara berurutan.',
                    ],
                ],
            ],
            ['title' => 'Ken Rosenberg Missions', 'slug' => 'ken-rosenberg-missions'],
            ['title' => 'Avery Carrington Missions', 'slug' => 'avery-carrington-missions'],
            ['title' => 'Colonel Cortez Missions', 'slug' => 'colonel-cortez-missions'],
            ['title' => 'Kent Paul Missions', 'slug' => 'kent-paul-missions'],
            ['title' => 'Ricardo Diaz Missions', 'slug' => 'ricardo-diaz-missions'],
            ['title' => 'Tommy Vercetti Missions', 'slug' => 'tommy-vercetti-missions'],
        ];

        foreach ($chapters as $index => $area) {
            $chapter = Chapter::updateOrCreate(
                [
                    'game_id' => $game->id,
                    'slug' => $area['slug'],
                ],
                [
                    'chapter_title' => $area['title'],
                    'section_title' => 'Walkthrough',
                    'overview' => $area['overview'] ?? null,
                    'overview_image' => 'coverimg/GTA_Vice_City.png',
                    'cover_image' => 'coverimg/GTA_Vice_City.png',
                    'source_url' => 'https://www.ign.com/wikis/grand-theft-auto-vice-city/In_the_Beginning..._%26_An_Old_Friend',
                    'order' => $index + 1,
                ],
            );

            $chapter->steps()->delete();

            foreach ($area['steps'] ?? [] as $stepIndex => $step) {
                Step::create([
                    'chapter_id' => $chapter->id,
                    'step_title' => $step['title'],
                    'content' => $step['content'],
                    'image_url' => $stepIndex === 0 ? 'coverimg/GTA_Vice_City.png' : null,
                    'order' => $stepIndex + 1,
                ]);
            }
        }
    }
}
