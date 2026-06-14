<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Review;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Spatie\Permission\Models\Role;

class AdminOverviewStats extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Users', User::count())
                ->description('Registered accounts')
                ->icon('heroicon-o-users'),
            Stat::make('Guides', Review::count())
                ->description('Walkthrough records')
                ->icon('heroicon-o-book-open'),
            Stat::make('Roles', Role::count())
                ->description('Access levels')
                ->icon('heroicon-o-shield-check'),
        ];
    }
}
