<?php

namespace App\Providers;

use App\Models\Chapter;
use App\Models\ContributionStep;
use App\Models\Game;
use App\Models\Review;
use App\Models\Step;
use App\Models\User;
use App\Models\WalkthroughContribution;
use App\Policies\ActivityPolicy;
use App\Policies\ChapterPolicy;
use App\Policies\ContributionStepPolicy;
use App\Policies\GamePolicy;
use App\Policies\ReviewPolicy;
use App\Policies\StepPolicy;
use App\Policies\UserPolicy;
use App\Policies\WalkthroughContributionPolicy;
use Filament\Actions\MountableAction;
use Filament\Notifications\Livewire\Notifications;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\VerticalAlignment;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;
use Spatie\Activitylog\Models\Activity;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Activity::class, ActivityPolicy::class);
        Gate::policy(Game::class, GamePolicy::class);
        Gate::policy(Chapter::class, ChapterPolicy::class);
        Gate::policy(Step::class, StepPolicy::class);
        Gate::policy(WalkthroughContribution::class, WalkthroughContributionPolicy::class);
        Gate::policy(ContributionStep::class, ContributionStepPolicy::class);
        Gate::policy(Review::class, ReviewPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Page::formActionsAlignment(Alignment::Right);
        Notifications::alignment(Alignment::End);
        Notifications::verticalAlignment(VerticalAlignment::End);
        Page::$reportValidationErrorUsing = function (ValidationException $exception) {
            Notification::make()
                ->title($exception->getMessage())
                ->danger()
                ->send();
        };
        MountableAction::configureUsing(function (MountableAction $action) {
            $action->modalFooterActionsAlignment(Alignment::Right);
        });
    }
}
