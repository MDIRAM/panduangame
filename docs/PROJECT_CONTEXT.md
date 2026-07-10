# Walkthrough Game Hub - Project Context

Dokumen ini adalah sumber konteks utama project. Baca dokumen ini sebelum mengubah kode agar fitur yang sudah ada tidak ditulis ulang atau dirusak.

## 1. Ringkasan Project

Walkthrough Game Hub adalah website walkthrough game berbasis database. Website menampilkan rute cerita, misi, langkah, teks, dan gambar walkthrough dalam tampilan sidebar seperti IGN.

Tujuan utama:

- Guest dapat melihat katalog game dan membaca walkthrough.
- User yang login dapat menyimpan game favorit dan memberikan rating.
- Super admin mengelola game dan seluruh isi walkthrough melalui Filament Admin Panel.
- Isi walkthrough tidak boleh di-hardcode di Blade.

Game yang saat ini menjadi fokus:

- Persona 3 Reload: konten utama sudah cukup lengkap.
- Dark Souls 2: sudah tersedia, tetapi masih dapat dilanjutkan.
- Elden Ring: sudah tersedia dan sedang dilanjutkan lewat admin panel.
- GTA Vice City: pernah dibuat sebagai contoh, kemudian dihapus agar dapat dibuat ulang dari admin panel.

## 2. Keputusan Product Saat Ini

Project ini bukan platform kontribusi publik.

- Guest hanya membaca.
- Member dapat favorite dan rating.
- Hanya `super_admin` yang dapat membuka `/admin` dan mengubah walkthrough.
- Fitur contribution lama masih memiliki model, migration, dan sebagian kode legacy, tetapi route frontend, API publik, dan resource adminnya dinonaktifkan/dihapus dari workflow.
- Jangan mengaktifkan kembali contribution tanpa keputusan product baru.
- Jangan membuat model `Guide`, `Article`, `Post`, atau `Walkthrough` baru.

## 3. Technology Stack

- PHP 8.3
- Laravel 12
- MariaDB 10.11
- Filament 3
- Livewire
- Spatie Laravel Permission
- Blade dan CSS untuk frontend
- Vite dan Tailwind untuk asset build
- Docker Compose untuk environment lokal

Domain lokal utama: `https://panduangame.test`

## 4. Struktur Domain Utama

Struktur walkthrough wajib dipertahankan:

```text
Game
  -> Chapter
       -> Step
```

### Game

Model: `app/Models/Game.php`

Tabel: `games`

Fungsi:

- Menyimpan identitas game.
- Menyimpan cover, deskripsi, status publish, dan featured.
- Memiliki banyak Chapter.
- Memiliki banyak favorite dan rating.

Field penting:

- `title`
- `slug`: identifier internal.
- `route_slug`: slug URL frontend.
- `description`
- `subtitle`
- `cover_image`
- `is_featured`
- `is_published`
- `content_status`: label progress konten untuk pengunjung, misalnya `complete`, `ongoing`, atau `upcoming`.

Catatan status:

- `is_published`: menentukan game tampil atau disembunyikan dari website publik.
- `is_featured`: menentukan game disorot di bagian spotlight homepage.
- `content_status`: hanya label progress walkthrough. Game tetap bisa published walaupun statusnya `ongoing`.

### Chapter

Model: `app/Models/Chapter.php`

Tabel: `chapters`

Chapter adalah satu halaman yang tampil sebagai item di sidebar walkthrough.

Contoh Chapter Elden Ring:

- Limgrave Barat
- Stormveil Castle
- Liurnia - South

Field penting:

- `game_id`: game pemilik chapter.
- `parent_id`: parent untuk sidebar bertingkat.
- `chapter_title`: nama halaman/sidebar.
- `slug`: slug unik di dalam game yang sama.
- `section_title`: nama kelompok sidebar, misalnya `Main Walkthrough`.
- `order`: urutan sidebar.
- `source_url`: sumber referensi.
- `overview`, `overview_image`, `cover_image`: field legacy/pendukung.

Relasi parent-child dipakai untuk struktur seperti:

```text
Ken Rosenberg Missions
  -> The Party
  -> Back Alley Brawl
  -> Jury Fury
  -> Riot
```

Chapter baru harus mengikuti `section_title` pertama milik game tersebut agar tidak membuat kelompok sidebar duplikat. Jika menjadi child, Chapter mengikuti section milik parent.

### Step

Model: `app/Models/Step.php`

Tabel: `steps`

Step adalah tempat penyimpanan isi walkthrough. Kolom `content` bertipe `longText` dan menyimpan HTML dari Rich Editor.

Editor terbaru memperlakukan satu Chapter sebagai satu dokumen seperti Word:

- Admin memilih Chapter dari sidebar editor.
- Admin menulis atau paste seluruh isi walkthrough pada satu Rich Editor.
- Ketika disimpan, Step lama pada Chapter tersebut digabung menjadi satu Step dokumen.
- `step_title` dokumen disamakan dengan `chapter_title`.
- Frontend tidak menampilkan judul Step jika sama dengan judul Chapter agar judul tidak ganda.

## 5. Logic Sidebar

Sidebar frontend dan editor admin berasal dari tabel `chapters`, bukan hardcode.

Aturan:

1. Chapter dikelompokkan berdasarkan `section_title`.
2. Chapter top-level memiliki `parent_id = null`.
3. Child Chapter memiliki `parent_id` ke Chapter top-level.
4. Urutan memakai kolom `order` ascending.
5. Chapter aktif diberi tampilan active.
6. Sidebar memiliki scroll sendiri saat daftar panjang.

Jangan membuat label kelompok baru hanya karena menambah Chapter. Gunakan section milik game yang sudah ada, biasanya `Main Walkthrough`.

## 6. Admin Walkthrough Workflow

Admin panel hanya dapat diakses user dengan role `super_admin`.

Pintu masuk pengelolaan walkthrough:

```text
Admin -> Games -> Manage Walkthrough
```

Alur:

1. Admin memilih game dari daftar Games.
2. Klik `Manage Walkthrough`.
3. Sidebar kiri hanya menampilkan Chapter dari game tersebut.
4. Admin memilih halaman sidebar.
5. Panel kanan menampilkan:
   - `Sidebar Page`: judul, parent, dan urutan.
   - `Walkthrough Document`: Rich Editor utama.
   - `Advanced Settings`: slug, source URL, overview legacy, dan cover.
6. Tombol `Add Sidebar Page` membuat Chapter baru untuk game aktif.
7. Slug dan section sidebar dibuat otomatis jika tidak diisi.
8. Save menyimpan metadata Chapter dan dokumen Step ke database.

Global menu Chapter dan Step disembunyikan dari navigasi Filament agar data berbagai game tidak tercampur dalam workflow utama. Resource dan route lamanya masih dipertahankan sebagai jalur teknis/cadangan.

File utama:

- `app/Filament/Admin/Resources/GameResource.php`
- `app/Filament/Admin/Resources/ChapterResource.php`
- `app/Filament/Admin/Resources/ChapterResource/Pages/EditChapter.php`
- `app/Filament/Admin/Resources/GameResource/RelationManagers/ChaptersRelationManager.php`
- `resources/views/filament/admin/resources/chapter-resource/pages/edit-chapter.blade.php`

## 7. Rich Editor dan Paste Normalizer

Rich Editor mendukung:

- Heading 2 dan Heading 3
- Paragraf
- Bold, italic, underline, dan strike
- Bullet list dan ordered list
- Blockquote
- Gambar di dalam dokumen

Gambar upload disimpan pada disk `public` di:

```text
storage/app/public/walkthrough/content
```

URL gambar disimpan relatif sebagai `/storage/...` agar aman ketika domain berubah saat deploy.

Paste normalizer membersihkan HTML dari situs referensi:

- Menghapus script, iframe, form, style, class, dan atribut event.
- Mengubah link menjadi highlight `<mark>` berwarna emas tanpa dapat diklik.
- Mengubah heading yang terlalu panjang menjadi paragraf.
- Menghapus bold berlebihan pada paragraf panjang.
- Menghapus caption gambar.
- Menjaga gambar tetap responsif.

Logic sanitizer:

- `app/Support/RichText.php`
- Script paste langsung berada di custom Blade editor Chapter.

Catatan: gambar hotlink dari situs lain dapat mati atau diblokir. Untuk konten stabil dan deploy VPS, upload gambar lewat tombol attachment Rich Editor.

## 8. Frontend Flow

Route utama:

- `/`: katalog game.
- `/games/{slug}`: halaman overview game.
- `/games/{gameSlug}/walkthrough/{chapterSlug}`: halaman detail walkthrough.
- `/dashboard`: library user, favorite, dan rating.
- `/login` dan `/register`: autentikasi user.

Route video tidak menjadi fitur aktif. Navigasi video dan halaman `/videos` dihapus agar project fokus pada walkthrough berbasis database.

Controller utama:

- `CatalogController`: homepage dan detail game.
- `WalkthroughController`: mengambil game, Chapter, Step, sidebar, previous, dan next.
- `DashboardController`: library user.
- `GameFavoriteController`: favorite game.
- `GameRatingController`: rating game.

Blade utama:

- `resources/views/welcome.blade.php`
- `resources/views/games/show.blade.php`
- `resources/views/walkthrough/chapter_show.blade.php`
- `resources/views/dashboard.blade.php`

Link `Admin Panel` sengaja tidak ditampilkan di frontend. Super admin membuka panel melalui `/admin` atau redirect setelah login.

## 9. Authentication dan Role

Model: `app/Models/User.php`

Role aktif:

- `member`: user biasa.
- `super_admin`: pengelola admin panel.
- `contributor`: role legacy, saat ini diperlakukan seperti user biasa karena contribution dinonaktifkan.

Aturan akses Filament:

```php
public function canAccessPanel(Panel $panel): bool
{
    return $this->hasRole('super_admin');
}
```

Registrasi frontend menyimpan user ke tabel `users` dan memberikan role `member`.

## 10. Favorite dan Rating

Model:

- `GameFavorite`
- `GameRating`

Tabel:

- `game_favorites`
- `game_ratings`

Aturan database:

- Satu user hanya dapat favorite satu game satu kali.
- Satu user hanya memiliki satu rating per game.
- Rating dapat diubah atau dihapus.
- Unique constraint: `user_id + game_id`.
- Favorite dan rating hanya tersedia untuk user login.
- Nilai di frontend berasal dari database, bukan dummy.

## 11. API Saat Ini

Prefix: `/api/v1`

Endpoint publik:

- `POST /auth/register`
- `POST /auth/login`
- `GET /games`
- `GET /games/{route_slug}`
- `GET /games/{route_slug}/chapters/{chapter_slug}`

Endpoint token:

- `GET /auth/me`
- `POST /auth/logout`

API walkthrough saat ini bersifat read-only. Create, update, dan delete walkthrough tetap dilakukan melalui Filament, bukan API.

## 12. Seeder dan Data

`DatabaseSeeder` memanggil:

- `RoleSeeder`
- `UserSeeder`
- `GameSeeder`
- `EldenRingSeeder`
- `DarkSouls2Seeder`

Persona 3 menggunakan data lokal yang dapat di-deploy. Scraper IGN sudah tidak menjadi workflow aktif karena pemblokiran bot dan risiko ketergantungan eksternal.

Seeder berguna untuk data awal dan development. Setelah aplikasi berjalan, perubahan konten dilakukan melalui admin panel dan tersimpan langsung ke database.

Jangan menjalankan `migrate:fresh --seed` pada database yang berisi perubahan admin penting karena perintah tersebut menghapus seluruh data.

## 13. File Map Penting

```text
src/
  app/
    Filament/Admin/Resources/    # CRUD dan editor admin
    Http/Controllers/            # Controller web
    Http/Controllers/Api/        # Controller API
    Models/                      # Model Eloquent
    Support/RichText.php         # Sanitizer walkthrough
  database/
    migrations/                  # Struktur database
    seeders/                     # Data awal
  resources/
    views/                       # Blade frontend dan custom admin
    css/filament/admin/          # Theme admin
  routes/
    web.php                      # Route frontend/auth
    api.php                      # Route API v1
  tests/Feature/                 # Test integrasi dan workflow
```

## 14. Development Commands

Dari root project:

```bash
docker compose up -d
docker compose exec -T php php artisan migrate
docker compose exec -T php php artisan test
docker compose exec -T php npm run build
```

Alias milik project/user jika tersedia:

```bash
dca migrate
dca route:list
```

Setelah mengubah Blade atau konfigurasi:

```bash
docker compose exec -T --user www-data php php artisan optimize:clear
docker compose exec -T --user www-data php php artisan view:cache
```

## 15. Aturan untuk Developer atau AI Berikutnya

1. Jangan mulai dari nol dan jangan rewrite project.
2. Baca file terkait dan git diff sebelum mengubah kode.
3. Pertahankan struktur `Game -> Chapter -> Step`.
4. Jangan membuat Guide/Article/Post baru.
5. Jangan hardcode daftar Chapter atau isi walkthrough di Blade.
6. Jangan mengaktifkan contribution tanpa persetujuan.
7. Jangan mengubah role dan API jika tidak berkaitan dengan tugas.
8. Jangan menghapus data user, favorite, rating, atau walkthrough.
9. Jangan menjalankan `migrate:fresh` kecuali diminta secara eksplisit dan data sudah aman.
10. Gunakan admin panel untuk perubahan konten rutin.
11. Setiap perubahan harus menjaga halaman Persona 3, Dark Souls 2, dan Elden Ring tetap berjalan.
12. Jalankan test yang relevan sebelum menyatakan pekerjaan selesai.

## 16. Prioritas Berikutnya

1. Lengkapi Chapter Elden Ring melalui admin panel.
2. Lengkapi Dark Souls 2 melalui admin panel.
3. Buat ulang GTA Vice City dari admin panel untuk membuktikan CRUD dinamis.
4. Audit kesiapan deploy VPS: environment production, storage link, backup database, cache, queue, HTTPS, dan permission.
5. BRD dan PRD dibuat setelah flow website stabil.

## 17. Sumber Konten

Website eksternal seperti IGN dan Fextralife hanya menjadi referensi struktur dan informasi. Hindari menyalin seluruh artikel secara verbatim.

Praktik yang disarankan:

- Tulis ulang panduan dalam bahasa Indonesia yang natural.
- Pertahankan istilah game berbahasa Inggris jika lebih jelas.
- Upload gambar yang memang boleh digunakan dan simpan secara lokal.
- Simpan URL referensi pada `source_url` untuk kebutuhan pencatatan.
