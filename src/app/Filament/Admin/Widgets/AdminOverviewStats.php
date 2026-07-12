<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Chapter;
use App\Models\ChapterComment;
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
        $publishedGames = Game::where('is_published', true)->count();
        $completeGames = Game::where('content_status', 'complete')->count();
        $ongoingGames = Game::where('content_status', 'ongoing')->count();

        return [
            Stat::make('Published Games', $publishedGames)
                ->description($completeGames . ' complete, ' . $ongoingGames . ' ongoing')
                ->descriptionIcon('heroicon-m-check-circle')
                ->icon('heroicon-o-puzzle-piece')
                ->color('success'),
            Stat::make('Walkthrough Chapters', Chapter::count())
                ->description('Sidebar pages across all games')
                ->descriptionIcon('heroicon-m-book-open')
                ->icon('heroicon-o-book-open')
                ->color('info'),
            Stat::make('Guide Steps', Step::count())
                ->description('Saved walkthrough content blocks')
                ->descriptionIcon('heroicon-m-document-text')
                ->icon('heroicon-o-list-bullet')
                ->color('warning'),
            Stat::make('Users', User::count())
                ->description('Registered reader accounts')
                ->descriptionIcon('heroicon-m-users')
                ->icon('heroicon-o-users')
                ->color('gray'),
            Stat::make('Comments', ChapterComment::count())
                ->description('Reader discussions on walkthrough pages')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color('info'),
        ];
    }
}
