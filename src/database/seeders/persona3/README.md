# Persona 3 CSV Imports

Simpan CSV hasil export browser di folder ini dengan nama sesuai slug chapter.

Contoh:

```text
prologue-april-7-april-18.csv
first-visit-to-tartarus-april-19-april-20.csv
full-moon-operation-may.csv
```

Kolom yang didukung:

```text
content,image_url
```

`GameSeeder` akan membaca semua file yang namanya cocok dengan slug chapter. Untuk
mengimpor satu file tanpa menjalankan seeder:

```bash
php artisan persona:import {slug} {path-ke-csv}
```
