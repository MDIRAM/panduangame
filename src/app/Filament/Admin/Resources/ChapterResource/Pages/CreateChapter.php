<?php

namespace App\Filament\Admin\Resources\ChapterResource\Pages;

use App\Filament\Admin\Resources\ChapterResource;
use App\Models\Chapter;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateChapter extends CreateRecord
{
    protected static string $resource = ChapterResource::class;

    public function getTitle(): string
    {
        return 'Add Sidebar Page';
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (blank($data['slug'] ?? null)) {
            $baseSlug = Str::slug($data['chapter_title']);
            $slug = $baseSlug;
            $suffix = 2;

            while (Chapter::where('game_id', $data['game_id'])->where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $suffix;
                $suffix++;
            }

            $data['slug'] = $slug;
        }

        if (blank($data['section_title'] ?? null)) {
            $parentSection = filled($data['parent_id'] ?? null)
                ? Chapter::whereKey($data['parent_id'])->value('section_title')
                : null;
            $existingSection = Chapter::where('game_id', $data['game_id'])
                ->whereNotNull('section_title')
                ->orderBy('order')
                ->value('section_title');

            $data['section_title'] = $parentSection ?: $existingSection ?: 'Main Walkthrough';
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
