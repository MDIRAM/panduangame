<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Chapter;
use App\Models\Game;
use App\Models\Step;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminOverviewStats extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Users', User::count())
                ->description('Registered accounts')
                ->icon('heroicon-o-users'),
            Stat::make('Games', Game::count())
                ->description('Published catalog')
                ->icon('heroicon-o-puzzle-piece'),
            Stat::make('Chapters', Chapter::count())
                ->description('Walkthrough sections')
                ->icon('heroicon-o-book-open'),
            Stat::make('Steps', Step::count())
                ->description('Guide instructions')
                ->icon('heroicon-o-list-bullet'),
        ];
    }
}
