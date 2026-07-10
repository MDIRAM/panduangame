<?php

namespace App\Filament\Admin\Resources\GameResource\Pages;

use App\Filament\Admin\Resources\ChapterResource;
use App\Filament\Admin\Resources\GameResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGame extends EditRecord
{
    protected static string $resource = GameResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('manageWalkthrough')
                ->label('Open Walkthrough Editor')
                ->icon('heroicon-o-book-open')
                ->url(function (): ?string {
                    $firstChapter = $this->getRecord()->chapters()->first();

                    return $firstChapter
                        ? ChapterResource::getUrl('edit', ['record' => $firstChapter])
                        : null;
                })
                ->visible(fn (): bool => $this->getRecord()->chapters()->exists()),
            Actions\DeleteAction::make(),
        ];
    }
}
