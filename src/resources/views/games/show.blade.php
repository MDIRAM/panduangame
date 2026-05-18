<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $game['title'] }} | Walkthrough Game Hub</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet"/>
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="welcome-page" style="background: radial-gradient(circle at top left, #0a1222 0%, #101a36 35%, #070b15 100%); background-color: #070b15;">
    <div class="welcome-shell">
        <main class="game-page-hero">
            <div class="welcome-header">
                <div class="game-hero-top">
                    <p class="brand-label">Walkthrough Game Hub</p>
                    <h1>{{ $game['title'] }}</h1>
                    <p class="welcome-intro">{{ $game['subtitle'] }}</p>
                </div>
                <div class="welcome-actions">
                    <a href="{{ url('/') }}" class="button secondary">Back to homepage</a>
                </div>
            </div>

            @php
                $detailCovers = [
                    'elden-ring' => asset('coverimg/EldenRing.png'),
                    'dark-souls-2' => asset('coverimg/Dark_Souls_2.jpg'),
                ];

                $coverUrl = $detailCovers[$slug] ?? route('cover', ['slug' => $slug]);
                $coverPosition = $slug === 'dark-souls-2' ? 'center bottom' : 'center';
            @endphp

            <div style="border-radius:20px;overflow:hidden;margin-top:1rem;">
                <div style="height:380px;background-image: url('{{ $coverUrl }}'); background-size: cover; background-position:{{ $coverPosition }};"></div>
            </div>

            @if ($slug === 'dark-souls-2')
                @php
                    $routeEntries = [
                        [
                            'id' => 'things-betwixt',
                            'title' => 'Things Betwixt',
                            'image' => 'Things Betwixt.png',
                            'steps' => [
                                'Setelah cinematic pembuka selesai, maju lewat semak-semak, seberangi jembatan, lalu masuk ke rumah di depan untuk masuk ke bagian character creation.',
                               'Keluar dari rumah, ambil item awal di dekat gerobak, lalu nyalakan bonfire pertama sebelum masuk ke jalur tutorial lewat mist gate.',
                                'Gunakan area tutorial ini untuk belajar spacing, dodge, backstab, dan dasar mengontrol musuh. Bersihkan jalur pendek di samping untuk loot awal.',
                                'Lanjutkan melewati celah terakhir dan ikuti jalan keluar area sampai rute terbuka menuju Majula.',
                            ],
                        ],
                        [
                            'id' => 'majula',
                            'title' => 'Majula',
                            'image' => 'Majula.png',
                            'steps' => [
                                'Anggap Majula sebagai hub utama. Nyalakan bonfire, bicara dengan Emerald Herald, dan ambil Estus Flask sebelum pergi ke area lain.',
                                'Jelajahi tebing, mansion, tunnel, dan area merchant untuk mendapatkan souls awal, Lifegems, Homeward Bones, serta key penting.',
                                'Buka toko Lenigrast jika sudah bisa, lalu gunakan Majula untuk level up, upgrade gear, dan bersiap sebelum masuk ke rute besar pertama.',
                                'Kalau sudah siap, lanjut ke jalur menuju Forest of Fallen Giants.',
                            ],
                        ],
                        [
                            'id' => 'forest-of-fallen-giants',
                            'title' => 'Forest of Fallen Giants',
                            'image' => 'Forest of Fallen Giants.png',
                            'steps' => [
                                'Masuk dari Majula dan lewati reruntuhan yang penuh infantry dengan hati-hati. Musuhnya sederhana, tapi kelompok kecil bisa cepat mengepungmu.',
                                'Buka shortcut kembali ke bonfire setiap ada kesempatan, lalu cek walkway atas dan ruangan sekitar untuk equipment awal serta key item.',
                                'Kalahkan The Last Giant untuk mendapatkan reward souls besar di awal game, lalu gunakan Soldier Key untuk membuka bagian yang terkunci.',
                                'Kamu juga bisa lanjut ke The Pursuer dan memakai rute burung untuk mencapai The Lost Bastille.',
                            ],
                        ],
                        [
                            'id' => 'heides-tower',
                            'title' => 'Heide\'s Tower of Flame',
                            'image' => 'heides_tower_of_flame_cathedral_of_blue.png',
                            'steps' => [
                                'Kembali ke Majula dan ambil jalur tunnel turun menuju Heide\'s Tower. Old Knights punya damage besar, jadi lawan satu per satu.',
                                'Lewati platform batu, aktifkan switch dengan mengalahkan musuh tertentu, dan buka jalur yang lebih aman di area tower.',
                                'Dragonrider adalah tujuan utama awal di sini. Setelah boss selesai, bicara dengan Licia sampai dialognya habis.',
                                'Area ini juga terhubung ke Cathedral of Blue dan nanti membantu membuka akses menuju Huntsman\'s Copse.',
                            ],
                        ],
                        [
                            'id' => 'cathedral-of-blue',
                            'title' => 'Cathedral of Blue',
                            'image' => 'Cathedral_Of_Blue.png',
                            'steps' => [
                                'Dari Heide\'s Tower, ambil jalur samping yang dijaga naga kalau kamu sudah siap untuk cabang optional yang lebih berat.',
                                'Kalahkan Old Dragonslayer untuk membuka akses ke area covenant Blue Sentinels dan ambil reward di sekitar sana.',
                                'Rute ini pendek dibanding area utama, tapi tetap layak dibersihkan sebelum lanjut lebih jauh.',
                            ],
                        ],
                        [
                            'id' => 'no-mans-wharf',
                            'title' => 'No-Man\'s Wharf',
                            'image' => 'no_mans_wharf.png',
                            'steps' => [
                                'Setelah Heide\'s Tower, lanjut ke wharf dan nyalakan bonfire di dekat pintu masuk.',
                                'Gunakan torch untuk area gelap dan masuk ke bangunan secara perlahan karena ambush sering muncul di sini.',
                                'Naik melalui area dock, bunyikan bell untuk memanggil kapal, lalu naik ke kapal untuk melawan Flexile Sentry.',
                                'Setelah boss selesai, gunakan rute kapal untuk lanjut menuju The Lost Bastille.',
                            ],
                        ],
                        [
                            'id' => 'lost-bastille',
                            'title' => 'The Lost Bastille',
                            'image' => 'Lost_Bastille.png',
                            'steps' => [
                                'The Lost Bastille bisa dicapai dari No-Man\'s Wharf atau setelah mengalahkan The Pursuer dan memakai rute burung.',
                                'Jelajahi cell, rooftop, dan hidden door sambil membuka shortcut kembali ke bonfire.',
                                'Bersiap menghadapi tekanan banyak musuh sebelum melawan Ruin Sentinels. Serang sabar dan jangan biarkan ketiganya menguasai arena.',
                                'Setelah rute Bastille selesai, lanjut menuju Sinners\' Rise.',
                            ],
                        ],
                        [
                            'id' => 'belfry-luna',
                            'title' => 'Belfry Luna',
                            'image' => 'iron_keep_belfry_sol.png',
                            'steps' => [
                                'Gunakan Pharros Lockstone di The Lost Bastille untuk membuka jalur Belfry Luna.',
                                'Tower optional ini kecil, tapi berbahaya karena ruang sempit dan musuh yang agresif.',
                                'Bersihkan rute bellkeeper untuk loot tambahan, lalu kembali ke jalur utama Bastille setelah selesai.',
                            ],
                        ],
                        [
                            'id' => 'sinners-rise',
                            'title' => 'Sinners\' Rise',
                            'image' => 'sinners_rise.png',
                            'steps' => [
                                'Dari The Lost Bastille, masuk ke Sinners\' Rise dan gunakan bonfire dekat pintu masuk sebagai titik persiapan.',
                                'Lewati area air dan prison dengan hati-hati, perhatikan serangan jarak jauh dan ambush musuh.',
                                'Kalau punya Bastille Key, nyalakan ruangan samping dekat arena boss agar fight Lost Sinner jadi lebih mudah.',
                                'Kalahkan Lost Sinner untuk mendapatkan salah satu major souls yang dibutuhkan untuk progres utama.',
                            ],
                        ],
                        [
                            'id' => 'huntsmans-copse',
                            'title' => 'Huntsman\'s Copse',
                            'image' => 'huntsmans_copse.png',
                            'steps' => [
                                'Setelah bicara dengan Licia dan memindahkannya ke Majula, bayar dia untuk memutar jalur dan membuka Huntsman\'s Copse.',
                                'Ikuti jalur hutan, nyalakan bonfire, dan bersiap menghadapi musuh melee cepat serta tekanan jarak jauh.',
                                'Cari key untuk membuka bonfire di hut terkunci, lalu lanjut ke jalur waterfall dan cave.',
                                'Lewati area skeleton dan bersiap untuk boss fight Skeleton Lords.',
                            ],
                        ],
                        [
                            'id' => 'undead-purgatory',
                            'title' => 'Undead Purgatory',
                            'image' => 'Undead_Purgatory.png',
                            'steps' => [
                                'Undead Purgatory adalah cabang optional dari Huntsman\'s Copse.',
                                'Seberangi jembatan dengan hati-hati, lalu masuk ke jalur arena kalau sudah siap menghadapi Executioner\'s Chariot.',
                                'Bersihkan area ini untuk akses covenant dan reward, lalu kembali ke jalur utama menuju Harvest Valley.',
                            ],
                        ],
                        [
                            'id' => 'harvest-valley',
                            'title' => 'Harvest Valley',
                            'image' => 'Harvest_Valley.png',
                            'steps' => [
                                'Bergerak dari Huntsman\'s Copse ke Harvest Valley dan hati-hati dengan poison pools.',
                                'Gunakan Lifegems, poison moss, dan rute yang aman untuk mengambil loot penting tanpa terlalu memaksa.',
                                'Lanjut melewati jalur windmill dan bergerak menuju Earthen Peak.',
                            ],
                        ],
                        [
                            'id' => 'earthen-peak',
                            'title' => 'Earthen Peak',
                            'image' => 'Earthen_Peak.png',
                            'steps' => [
                                'Earthen Peak penuh tekanan poison, walkway sempit, dan titik ambush.',
                                'Cari interaksi windmill sebelum masuk rute boss utama; ini bisa membuat fight Mytha jauh lebih mudah.',
                                'Bersihkan jalur atas, kalahkan Mytha, lalu lanjut menuju rute Iron Keep.',
                            ],
                        ],
                        [
                            'id' => 'grave-of-saints',
                            'title' => 'The Grave of Saints',
                            'image' => 'The_Grave_of_Saints.png',
                            'steps' => [
                                'Kembali ke Majula dan turun ke pit saat health sudah cukup, memakai Silvercat Ring, atau sudah punya akses ladder.',
                                'The Grave of Saints adalah rute optional rat covenant dengan mekanik Pharros dan encounter musuh di ruang sempit.',
                                'Bersihkan area ini kalau ingin reward tambahan, lalu lanjut lebih dalam menuju The Gutter.',
                            ],
                        ],
                        [
                            'id' => 'the-pit',
                            'title' => 'The Pit',
                            'image' => 'ThePit.png',
                            'steps' => [
                                'Pit di Majula adalah pintu masuk menuju rute bawah game.',
                                'Turun secara bertahap, pastikan health cukup, dan siapkan healing item agar aman.',
                                'Jalur turun ini menghubungkan Grave of Saints, The Gutter, dan akhirnya Black Gulch.',
                            ],
                        ],
                        [
                            'id' => 'the-gutter',
                            'title' => 'The Gutter',
                            'image' => 'TheGutter.png',
                            'steps' => [
                                'The Gutter gelap, vertikal, dan mudah bikin tersesat. Bawa torch dan bergerak dari platform ke platform.',
                                'Nyalakan sconce sebagai penanda, waspadai poison statues, dan jangan terburu-buru melewati struktur kayu rapuh.',
                                'Terus turun sampai rute terbuka menuju Black Gulch.',
                            ],
                        ],
                        [
                            'id' => 'black-gulch',
                            'title' => 'Black Gulch',
                            'image' => 'Black_Gulch.png',
                            'steps' => [
                                'Black Gulch pendek, tapi menyiksa karena poison statues memenuhi sebagian besar jalur.',
                                'Hancurkan statues sambil maju, gunakan bonfire sebagai titik reset, dan perhatikan drop tersembunyi serta side path.',
                                'Kalahkan The Rotten di akhir rute untuk mendapatkan major soul berikutnya.',
                            ],
                        ],
                        [
                            'id' => 'shaded-woods',
                            'title' => 'Shaded Woods',
                            'image' => 'Shaded_Woods.png',
                            'steps' => [
                                'Dari Majula, bebaskan Rosabeth dari petrify untuk membuka jalur menuju Shaded Woods.',
                                'Capai bonfire Ruined Fork Road, lalu pilih cabang rute dengan hati-hati; area fog membuat posisi musuh sulit dibaca.',
                                'Lanjut melewati Shaded Ruins dan bersiap menghadapi Scorpioness Najka di bagian akhir area.',
                                'Setelah Najka, jalur akan berlanjut menuju Doors of Pharros.',
                            ],
                        ],
                        [
                            'id' => 'doors-of-pharros',
                            'title' => 'Doors of Pharros',
                            'image' => 'Doors of Pharros.png',
                            'steps' => [
                                'Doors of Pharros adalah area banjir setelah Shaded Woods, dengan gerakan yang melambat dan beberapa musuh besar.',
                                'Jangan memakai semua Pharros Lockstone secara asal; beberapa contraption hanya jebakan atau tidak terlalu bernilai.',
                                'Putari bagian atas ruang utama untuk menemukan rute bonfire dan jalan menuju Royal Rat Authority.',
                                'Setelah selesai, lanjutkan perjalanan menuju Brightstone Cove Tseldora.',
                            ],
                        ],
                    ];
                @endphp

                <section class="wiki-route-page">
                    <header class="wiki-route-header">
                        <p>Dark Souls 2 Wiki Guide</p>
                        <h2>Game Progress Route</h2>
                        <span>Urutan area yang direkomendasikan untuk playthrough pertama.</span>
                    </header>

                    <nav class="wiki-route-toc" aria-label="Progress route navigation">
                        @foreach ($routeEntries as $entry)
                            <a href="#{{ $entry['id'] }}">{{ $entry['title'] }}</a>
                        @endforeach
                    </nav>

                    @foreach ($routeEntries as $entry)
                        @php
                            $imagePath = 'coverimg/DS2/'.$entry['image'];
                            $hasImage = file_exists(public_path($imagePath));
                        @endphp

                        <article class="wiki-route-entry" id="{{ $entry['id'] }}">
                            <div class="wiki-route-media">
                                <h3>{{ $entry['title'] }}</h3>
                                @if ($hasImage)
                                    <img src="{{ asset($imagePath) }}" alt="{{ $entry['title'] }} area">
                                @else
                                    <div class="wiki-route-image-placeholder">{{ $entry['title'] }}</div>
                                @endif
                            </div>

                            <div class="wiki-route-copy">
                                @foreach ($entry['steps'] as $step)
                                    <p>{!! str_replace(['Majula', 'Lost Sinner', 'The Rotten', 'Royal Rat Authority', 'Brightstone Cove Tseldora'], ['<strong>Majula</strong>', '<strong>Lost Sinner</strong>', '<strong>The Rotten</strong>', '<strong>Royal Rat Authority</strong>', '<strong>Brightstone Cove Tseldora</strong>'], e($step)) !!}</p>
                                @endforeach
                            </div>
                        </article>
                    @endforeach

                    <section class="wiki-map-section">
                        <h2>World Inter-connectivity</h2>
                        <p>Klik untuk memperbesar.</p>
                        <a href="{{ asset('coverimg/DS2/DS2map2.jpg') }}" target="_blank" rel="noopener">
                            <img src="{{ asset('coverimg/DS2/DS2map2.jpg') }}" alt="Dark Souls 2 world inter-connectivity map">
                        </a>
                    </section>

                    <section class="quick-walkthrough-section">
                        <h2>Quick Walkthrough</h2>

                        <p>Untuk mencapai <strong>Drangleic Castle</strong>, kumpulkan empat Great Souls: <strong>The Lost Sinner</strong>, <strong>Old Iron King</strong>, <strong>The Rotten</strong>, dan <strong>The Duke's Dear Freja</strong>. Alternatifnya, Soul Memory yang cukup tinggi juga bisa membuka akses lewat rute <strong>Shrine of Winter</strong>.</p>

                        <h3>4 Great Souls:</h3>
                        <ol>
                            <li><strong>The Lost Sinner</strong> dicapai lewat Forest of Fallen Giants, The Lost Bastille, dan Sinners' Rise. Bastille Key membantu menyalakan arena agar boss fight lebih mudah.</li>
                            <li><strong>The Rotten</strong> dicapai dengan turun ke pit di Majula. Lanjutkan lewat Grave of Saints, The Gutter, dan Black Gulch sampai boss di akhir area.</li>
                            <li><strong>Old Iron King</strong> dicapai dengan membuka jalur dari Majula ke Heide's Tower of Flame, lalu lanjut melalui Huntsman's Copse, Harvest Valley, Earthen Peak, dan Iron Keep.</li>
                            <li><strong>The Duke's Dear Freja</strong> berada di Brightstone Cove Tseldora. Capai area ini dengan membuka rute Shaded Woods, lalu melewati Shaded Ruins dan Doors of Pharros.</li>
                        </ol>

                        <h3>Mengakses Shrine of Winter:</h3>
                        <ol>
                            <li>Mulai dari Majula, pergi ke Shaded Woods, dan capai bonfire Ruined Fork Road. Shrine of Winter terbuka setelah mengalahkan empat primal bosses atau memenuhi kebutuhan Soul Memory untuk cycle playthrough kamu.</li>
                        </ol>

                        <h3>Mencari King:</h3>
                        <ol>
                            <li>Ikuti jalur menuju <strong>Drangleic Castle</strong>. Gunakan musuh di dekat golem untuk membuka pintu utama, lalu lanjutkan rute castle.</li>
                            <li>Lanjutkan progress sampai bertemu Nashandra, kemudian teruskan ke rute Looking Glass Knight dan area setelahnya.</li>
                            <li>Masuk ke Shrine of Amana, kalahkan Demon of Song, lalu lanjut ke Undead Crypt. Setelah mengalahkan Velstadt, ambil King's Ring di belakangnya.</li>
                        </ol>

                        <h3>Endgame:</h3>
                        <ol>
                            <li>Gunakan King's Ring di pintu tersegel Shaded Woods untuk mencapai Aldia's Keep, Dragon Aerie, dan Dragon Shrine. Bicara dengan Ancient Dragon untuk mendapatkan Ashen Mist Heart.</li>
                            <li>Kembali ke Forest of Fallen Giants dan gunakan Ashen Mist Heart untuk masuk ke Memory of Jeigh. Kalahkan Giant Lord untuk mendapatkan Giant's Kinship.</li>
                            <li>Warp kembali ke Drangleic Castle, buka King's Gate, lalu hadapi rangkaian final boss: Throne Watcher, Throne Defender, dan Nashandra.</li>
                        </ol>
                    </section>
                </section>
            @endif

            @if ($slug === 'persona-3')
                @php
                    $storyMissions = [
                        'April' => [
                            ['title' => 'Prologue (April 7 - April 18) Walkthrough', 'slug' => 'prologue-april-7-april-18'],
                            ['title' => 'First Visit to Tartarus (April 19 - April 20) Walkthrough', 'slug' => 'first-visit-to-tartarus-april-19-april-20'],
                        ],
                        'May' => [
                            ['title' => 'Full Moon Operation - May', 'slug' => 'full-moon-operation-may'],
                        ],
                        'June' => [
                            ['title' => 'Full Moon Operation - June', 'slug' => 'full-moon-operation-june'],
                            ['title' => 'Theurgy Field Test (June 13)', 'slug' => 'theurgy-field-test-june-13'],
                        ],
                        'July' => [
                            ['title' => 'Full Moon Operation - July', 'slug' => 'full-moon-operation-july'],
                            ['title' => 'Summer Vacation (July 20 - July 23)', 'slug' => 'summer-vacation-july-20-july-23'],
                        ],
                        'August' => [
                            ['title' => 'Full Moon Operation - August', 'slug' => 'full-moon-operation-august'],
                            ['title' => 'Shadow of the Abyss Story Event (August 14)', 'slug' => 'shadow-of-the-abyss-story-event-august-14'],
                        ],
                        'September' => [
                            ['title' => 'Full Moon Operation - September', 'slug' => 'full-moon-operation-september'],
                        ],
                        'October' => [
                            ['title' => 'Full Moon Operation - October', 'slug' => 'full-moon-operation-october'],
                        ],
                        'November' => [
                            ['title' => 'Full Moon Operation - November', 'slug' => 'full-moon-operation-november'],
                            ['title' => 'School Trip (November 17 - November 20)', 'slug' => 'school-trip-november-17-november-20'],
                            ['title' => 'Chidori Battle (November 22)', 'slug' => 'chidori-battle-november-22'],
                        ],
                        'December' => [
                            ['title' => 'Final Mission: The Promised Day (January 31)', 'slug' => 'final-mission-the-promised-day-january-31'],
                        ],
                    ];
                @endphp

                <section class="persona-story-page">
                    <header class="persona-story-header">
                        <h2>Story Mission Walkthroughs</h2>
                        <p>The story missions below have been separated into the months they take place in.</p>
                    </header>

                    <div class="persona-month-list">
                        @foreach ($storyMissions as $month => $missions)
                            <section class="persona-month-section" id="persona-{{ str($month)->lower() }}">
                                <h3>{{ $month }}</h3>
                                <ul>
                                    @foreach ($missions as $mission)
                                        <li>
                                            <a href="{{ route('persona.story.show', ['mission' => $mission['slug']]) }}">
                                                {{ $mission['title'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </section>
                        @endforeach
                    </div>
                </section>
            @endif
        </main>
    </div>
</body>
</html>
