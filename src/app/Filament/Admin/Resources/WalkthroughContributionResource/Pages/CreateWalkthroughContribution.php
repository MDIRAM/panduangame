<?php

namespace App\Filament\Admin\Resources\WalkthroughContributionResource\Pages;

use App\Filament\Admin\Resources\WalkthroughContributionResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateWalkthroughContribution extends CreateRecord
{
    protected static string $resource = WalkthroughContributionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['slug'] = Str::slug($data['title']).'-'.Str::lower(Str::random(6));

        return $data;
    }
}
