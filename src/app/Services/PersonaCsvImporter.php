<?php

namespace App\Services;

use App\Models\Chapter;
use App\Models\Step;
use Illuminate\Support\Facades\DB;

class PersonaCsvImporter
{
    public function import(Chapter $chapter, string $csvPath, bool $replace = true): int
    {
        $handle = fopen($csvPath, 'r');

        if ($handle === false) {
            return 0;
        }

        $firstRow = fgetcsv($handle, 0, ',');

        if ($firstRow === false) {
            fclose($handle);

            return 0;
        }

        $hasHeader = $this->looksLikeHeader($firstRow);
        $headers = $hasHeader ? array_map($this->normalize(...), $firstRow) : [];
        $rows = $hasHeader ? [] : [$firstRow];

        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            $rows[] = $row;
        }

        fclose($handle);

        $contentIndex = $this->findContentIndex($headers);
        $imageIndex = $this->findImageIndex($headers);

        return DB::transaction(function () use ($chapter, $rows, $contentIndex, $imageIndex, $replace) {
            if ($replace) {
                $chapter->steps()->delete();
            }

            $order = $replace ? 1 : ((int) $chapter->steps()->max('order')) + 1;
            $imported = 0;

            foreach ($rows as $row) {
                $content = $this->extractContent($row, $contentIndex);

                if ($content === '') {
                    continue;
                }

                Step::create([
                    'chapter_id' => $chapter->id,
                    'step_title' => 'Step '.$order,
                    'content' => $content,
                    'image_url' => $this->extractImageUrl($row, $imageIndex),
                    'order' => $order,
                ]);

                $order++;
                $imported++;
            }

            return $imported;
        });
    }

    private function looksLikeHeader(array $row): bool
    {
        foreach ($row as $cell) {
            $header = $this->normalize($cell);

            if (str_contains($header, 'content')
                || str_contains($header, 'paragraph')
                || str_contains($header, 'image')
                || str_contains($header, 'img')
                || str_contains($header, 'src')
                || str_starts_with($header, '/section.')) {
                return true;
            }
        }

        return false;
    }

    private function findContentIndex(array $headers): ?int
    {
        foreach ($headers as $index => $header) {
            if (in_array($header, ['content', 'text', 'paragraph', 'description'], true)
                || str_ends_with($header, '/p')) {
                return $index;
            }
        }

        return null;
    }

    private function findImageIndex(array $headers): ?int
    {
        foreach ($headers as $index => $header) {
            if (in_array($header, ['image_url', 'image', 'img', 'src'], true)
                || (str_contains($header, 'img') && str_contains($header, 'src'))) {
                return $index;
            }
        }

        return null;
    }

    private function extractContent(array $row, ?int $contentIndex): string
    {
        if ($contentIndex !== null) {
            return $this->cleanText($row[$contentIndex] ?? '');
        }

        foreach ($row as $cell) {
            $value = trim($cell);

            if ($value !== ''
                && ! filter_var($value, FILTER_VALIDATE_URL)
                && ! str_starts_with($value, '/section.')) {
                return $this->cleanText($value);
            }
        }

        return '';
    }

    private function extractImageUrl(array $row, ?int $imageIndex): ?string
    {
        if ($imageIndex !== null) {
            $candidate = trim($row[$imageIndex] ?? '');

            if ($this->isImageUrl($candidate)) {
                return $candidate;
            }
        }

        foreach ($row as $cell) {
            $candidate = trim($cell);

            if ($this->isImageUrl($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private function isImageUrl(string $url): bool
    {
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $host = parse_url($url, PHP_URL_HOST) ?? '';

        return str_contains($host, 'ignimgs.com')
            || (bool) preg_match('/\.(jpg|jpeg|png|webp)(\?.*)?$/i', $url);
    }

    private function cleanText(string $value): string
    {
        return trim(preg_replace('/\s+/u', ' ', strip_tags($value)) ?? '');
    }

    private function normalize(string $value): string
    {
        return strtolower(trim($value));
    }
}
