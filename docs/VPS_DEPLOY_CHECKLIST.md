# VPS Deploy Checklist

Checklist singkat sebelum dan saat deploy Walkthrough Game Hub ke VPS.

## Status Project

- Frontend utama sudah database-driven.
- Admin mengelola `Game -> Chapter -> Step` dari Filament.
- User login dapat favorite dan rating game.
- Contribution publik dinonaktifkan.
- Video dinonaktifkan dari navigasi sampai ada konten video yang benar-benar siap.

## Sebelum Beli VPS

- Pastikan provider mendukung Ubuntu LTS.
- Pilih VPS minimal 2 GB RAM untuk Laravel + database kecil.
- Siapkan domain dan akses DNS.
- Siapkan backup database lokal jika data sudah penting.

## Setelah VPS Aktif

1. Install stack:
   - Nginx
   - PHP 8.3+
   - MariaDB/MySQL
   - Composer
   - Node.js
   - Git
2. Clone project ke server.
3. Buat file `.env` production.
4. Set database production.
5. Jalankan:

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Permission Wajib

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

Kalau muncul error `file_put_contents(...storage/framework/views...): Permission denied`, ulangi permission di atas.

## ENV Penting

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-kamu.com
FILESYSTEM_DISK=public
CACHE_STORE=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database
```

## Setelah Online

- Cek homepage.
- Cek halaman game.
- Cek halaman walkthrough dengan sidebar.
- Cek login/register.
- Cek favorite dan rating.
- Cek `/admin` dengan akun `super_admin`.
- Cek upload gambar di Rich Editor.
- Cek gambar tampil dari `/storage/...`.

## Catatan Video

Fitur video belum perlu ditampilkan. Untuk presentasi dan deploy awal, lebih aman fokus ke walkthrough teks, gambar, sidebar, admin panel, favorite, dan rating.
