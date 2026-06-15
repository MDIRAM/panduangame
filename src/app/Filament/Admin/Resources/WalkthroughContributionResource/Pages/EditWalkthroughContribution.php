<?php

namespace App\Filament\Admin\Resources\WalkthroughContributionResource\Pages;

use App\Filament\Admin\Resources\WalkthroughContributionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWalkthroughContribution extends EditRecord
{
    protected static string $resource = WalkthroughContributionResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
