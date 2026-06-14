SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE steps;
TRUNCATE TABLE chapters;
TRUNCATE TABLE games;
SET FOREIGN_KEY_CHECKS = 1;

INSERT INTO games (id, title, slug, description, cover_image, created_at, updated_at) VALUES
(1, 'Persona 3 Reload', 'persona-3-reload', 'Full Walkthrough and Schedule Guide.', 'https://ignimgs.com', NOW(), NOW());

INSERT INTO chapters (id, game_id, chapter_title, slug, `order`, created_at, updated_at) VALUES
(1, 1, 'April Walkthrough', 'april-walkthrough', 1, NOW(), NOW()),
(2, 1, 'May Walkthrough', 'may-walkthrough', 2, NOW(), NOW()),
(3, 1, 'June Walkthrough', 'june-walkthrough', 3, NOW(), NOW()),
(4, 1, 'July Walkthrough', 'july-walkthrough', 4, NOW(), NOW()),
(5, 1, 'August Walkthrough', 'august-walkthrough', 5, NOW(), NOW()),
(6, 1, 'September Walkthrough', 'september-walkthrough', 6, NOW(), NOW()),
(7, 1, 'October Walkthrough', 'october-walkthrough', 7, NOW(), NOW()),
(8, 1, 'November Walkthrough', 'november-walkthrough', 8, NOW(), NOW()),
(9, 1, 'December Walkthrough', 'december-walkthrough', 9, NOW(), NOW()),
(10, 1, 'January Walkthrough', 'january-walkthrough', 10, NOW(), NOW());

INSERT INTO steps (chapter_id, step_title, content, image_url, `order`, created_at, updated_at) VALUES
(1, 'Prologue (April 7)', 'Misi dimulai saat karakter utama tiba di Stasiun Iwatodai. Berjalanlah menuju Gedung Asrama Siswa untuk memicu cutscene pertama.', 'https://ignimgs.com', 1, NOW(), NOW()),
(1, 'First Visit to Tartarus', 'Jelajahi Menara Tartarus untuk pertama kalinya. Ikuti instruksi pertarungan Turn-Based dan gunakan serangan fisik untuk knockdown musuh.', 'https://ignimgs.com', 2, NOW(), NOW()),
(2, 'Full Moon Operation - May', 'Selamatkan warga di dalam gerbong kereta monorail. Gunakan skill Sihir Es Mitsuru untuk mengalahkan boss Arcana Priestess sebelum waktu habis.', 'https://ignimgs.com', 1, NOW(), NOW()),
(3, 'Full Moon Operation - June', 'Operasi bertempat di hotel pelabuhan. Hadapi boss Empress dan Emperor. Pastikan level Junpei sudah cukup untuk All-Out Attack.', 'https://ignimgs.com', 1, NOW(), NOW()),
(4, 'Full Moon Operation - July', 'Menghadapi boss raksasa di pangkalan militer lepas pantai bersama anggota tim baru, Aegis.', 'https://ignimgs.com', 1, NOW(), NOW()),
(5, 'Full Moon Operation - August', 'Pertarungan bawah tanah melawan komplotan Strega yang mencoba menghalangi misi SEES.', 'https://ignimgs.com', 1, NOW(), NOW()),
(6, 'September Content', 'Eksplorasi blok baru di Tartarus dan maksimalkan hubungan Social Link sebelum ujian semester.', 'https://ignimgs.com', 1, NOW(), NOW()),
(7, 'October Content', 'Mempersiapkan tim menghadapi malam Full Moon paling krusial yang mengungkap kebenaran Dark Hour.', 'https://ignimgs.com', 1, NOW(), NOW()),
(8, 'November Content', 'Cerita musim gugur yang emosional. Fokus pada peningkatan stats akademi untuk membuka Persona tingkat tinggi.', 'https://ignimgs.com', 1, NOW(), NOW()),
(9, 'December Content', 'Memasuki fase akhir penentuan nasib dunia. Pilihan penting di malam natal akan menentukan ending game.', 'https://ignimgs.com', 1, NOW(), NOW()),
(10, 'Nyx Final Battle (January)', 'Mencapai puncak tertinggi Tartarus untuk menantang Avatar Nyx dalam pertarungan bos terakhir yang epik dan menamatkan game.', 'https://ignimgs.com', 1, NOW(), NOW());
