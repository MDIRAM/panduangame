<?php

namespace App\Filament\Admin\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;

class LatestAccessLogs extends BaseWidget
{
    use HasWidgetShield;
    protected static ?int $sort = 100;

    protected static ?string $heading = 'Recent Admin Activity';

    protected int|string|array $columnSpan = 'full';

    protected static function getLogNameColors(): array
    {
        $customs = [];

        foreach (config('filament-logger.custom') ?? [] as $custom) {
            if (filled($custom['color'] ?? null)) {
                $customs[$custom['color']] = $custom['log_name'];
            }
        }

        return array_merge(
            (config('filament-logger.resources.enabled') && config('filament-logger.resources.color')) ? [
                config('filament-logger.resources.color') => config('filament-logger.resources.log_name'),
            ] : [],
            (config('filament-logger.models.enabled') && config('filament-logger.models.color')) ? [
                config('filament-logger.models.color') => config('filament-logger.models.log_name'),
            ] : [],
            (config('filament-logger.access.enabled') && config('filament-logger.access.color')) ? [
                config('filament-logger.access.color') => config('filament-logger.access.log_name'),
            ] : [],
            (config('filament-logger.notifications.enabled') && config('filament-logger.notifications.color')) ? [
                config('filament-logger.notifications.color') => config('filament-logger.notifications.log_name'),
            ] : [],
            $customs,
        );
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Activity::query()->latest()->take(6)
            )
            ->columns([
                Tables\Columns\TextColumn::make('log_name')
                    ->badge()
                    ->colors(static::getLogNameColors())
                    ->label('Type')
                    ->formatStateUsing(fn ($state) => ucwords($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('event')
                    ->label('Event')
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Activity')
                    ->wrap(),

                Tables\Columns\TextColumn::make('subject_type')
                    ->label('Target')
                    ->formatStateUsing(function ($state, Model $record) {
                        /** @var Activity $record */
                        if (! $state) {
                            return '-';
                        }

                        return Str::of($state)->afterLast('\\')->headline() . ' # ' . $record->subject_id;
                    }),

                Tables\Columns\TextColumn::make('causer.name')
                    ->label('Admin'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Time')
                    ->dateTime(config('d/m/Y H:i A'))
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
