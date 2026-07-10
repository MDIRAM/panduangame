<?php

namespace App\Filament\Admin\Resources\ChapterResource\Pages;

use App\Filament\Admin\Resources\ChapterResource;
use App\Models\Chapter;
use App\Support\RichText;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Collection;

class EditChapter extends EditRecord
{
    protected static string $resource = ChapterResource::class;

    protected static string $view = 'filament.admin.resources.chapter-resource.pages.edit-chapter';

    public function getTitle(): string
    {
        return 'Walkthrough Editor';
    }

    public function getHeading(): string
    {
        return $this->getRecord()->chapter_title;
    }

    public function getSubheading(): ?string
    {
        return $this->getRecord()->game->title;
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    /**
     * @return Collection<int, Chapter>
     */
    public function getChapterTree(): Collection
    {
        return $this->getRecord()
            ->game
            ->chapters()
            ->whereNull('parent_id')
            ->with(['children' => fn ($query) => $query->orderBy('order')])
            ->orderBy('order')
            ->get();
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['document_content'] = $this->buildDocumentFromSteps();

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['document_content']);

        return $data;
    }

    protected function afterSave(): void
    {
        $content = RichText::sanitizeWalkthrough(
            (string) ($this->data['document_content'] ?? ''),
        );
        $chapter = $this->getRecord();

        $chapter->steps()->delete();

        if ($content === '') {
            return;
        }

        $chapter->steps()->create([
            'step_title' => $chapter->chapter_title,
            'content' => $content,
            'image_url' => null,
            'order' => 1,
        ]);
    }

    private function buildDocumentFromSteps(): string
    {
        $steps = $this->getRecord()->steps()->orderBy('order')->get();

        if ($steps->count() === 1
            && $steps->first()->step_title === $this->getRecord()->chapter_title
            && blank($steps->first()->image_url)) {
            return RichText::sanitizeWalkthrough($steps->first()->content);
        }

        $document = $steps
            ->map(function ($step): string {
                $title = filled($step->step_title)
                    ? '<h2>' . e($step->step_title) . '</h2>'
                    : '';
                $content = strip_tags($step->content) === $step->content
                    ? '<p>' . nl2br(e($step->content)) . '</p>'
                    : $step->content;
                $imageUrl = $this->getPortableImageUrl($step->image_url);
                $image = filled($imageUrl)
                    ? '<p><img src="' . e($imageUrl) . '" alt="' . e($step->step_title ?: $this->getRecord()->chapter_title) . '"></p>'
                    : '';

                return $title . $content . $image;
            })
            ->implode("\n");

        return RichText::sanitizeWalkthrough($document);
    }

    private function getPortableImageUrl(?string $path): ?string
    {
        if (blank($path) || str_starts_with($path, 'http')) {
            return $path;
        }

        if (is_file(public_path($path))) {
            return '/' . ltrim($path, '/');
        }

        return '/storage/' . ltrim($path, '/');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('addSidebarPage')
                ->label('Add Sidebar Page')
                ->icon('heroicon-o-plus')
                ->url(fn (): string => ChapterResource::getUrl('create', [
                    'game_id' => $this->getRecord()->game_id,
                ])),
            Actions\DeleteAction::make()
                ->label('Delete Page'),
        ];
    }
}
