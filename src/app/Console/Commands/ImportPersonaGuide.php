<?php

namespace App\Console\Commands;

use App\Models\Chapter;
use App\Services\PersonaCsvImporter;
use Illuminate\Console\Command;

class ImportPersonaGuide extends Command
{
    protected $signature = 'persona:import
        {slug : Slug chapter Persona 3}
        {csv : Path file CSV}
        {--append : Tambahkan data tanpa menghapus step lama}';

    protected $description = 'Import teks dan gambar walkthrough Persona 3 dari CSV hasil export browser';

    public function handle(PersonaCsvImporter $importer): int
    {
        $chapter = Chapter::query()
            ->whereHas('game', fn ($query) => $query->where('slug', 'persona-3-reload'))
            ->where('slug', $this->argument('slug'))
            ->first();

        if (! $chapter) {
            $this->error('Chapter tidak ditemukan. Jalankan GameSeeder terlebih dahulu.');

            return self::FAILURE;
        }

        $csvPath = $this->resolveCsvPath($this->argument('csv'));

        if (! is_file($csvPath)) {
            $this->error('File CSV tidak ditemukan: '.$csvPath);

            return self::FAILURE;
        }

        $count = $importer->import($chapter, $csvPath, ! $this->option('append'));

        $this->info($count.' step berhasil diimpor ke '.$chapter->chapter_title.'.');

        return self::SUCCESS;
    }

    private function resolveCsvPath(string $path): string
    {
        if (str_starts_with($path, '/')) {
            return $path;
        }

        return base_path($path);
    }
}
