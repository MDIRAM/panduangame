<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\WalkthroughContributionResource\Pages;
use App\Models\WalkthroughContribution;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WalkthroughContributionResource extends Resource
{
    protected static ?string $model = WalkthroughContribution::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Community';

    protected static ?string $navigationLabel = 'Walkthrough Submissions';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Submission')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('user_id')
                        ->relationship('author', 'name')
                        ->label('Author')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\Select::make('game_id')
                        ->relationship('game', 'title')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(150)
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('summary')
                        ->required()
                        ->rows(5)
                        ->columnSpanFull(),
                    Forms\Components\Select::make('status')
                        ->options(WalkthroughContribution::statuses())
                        ->required(),
                    Forms\Components\Textarea::make('moderation_notes')
                        ->label('Moderation notes')
                        ->rows(4)
                        ->columnSpanFull(),
                ]),
            Forms\Components\Section::make('Walkthrough Steps')
                ->schema([
                    Forms\Components\Repeater::make('steps')
                        ->relationship()
                        ->orderColumn('order')
                        ->reorderable()
                        ->collapsed()
                        ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                        ->schema([
                            Forms\Components\TextInput::make('title')
                                ->required()
                                ->maxLength(150),
                            Forms\Components\TextInput::make('order')
                                ->numeric()
                                ->minValue(1)
                                ->required(),
                            Forms\Components\Textarea::make('content')
                                ->required()
                                ->rows(6)
                                ->columnSpanFull(),
                            Forms\Components\FileUpload::make('image_path')
                                ->disk('public')
                                ->directory('contributions/admin')
                                ->image()
                                ->maxSize(4096)
                                ->columnSpanFull(),
                        ])
                        ->columns(2)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('submitted_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('game.title')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('author.name')
                    ->label('Author')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('steps_count')
                    ->counts('steps')
                    ->label('Steps'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        WalkthroughContribution::STATUS_PUBLISHED => 'success',
                        WalkthroughContribution::STATUS_PENDING => 'warning',
                        WalkthroughContribution::STATUS_REJECTED => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('submitted_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(WalkthroughContribution::statuses()),
                Tables\Filters\SelectFilter::make('game_id')
                    ->relationship('game', 'title')
                    ->label('Game'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWalkthroughContributions::route('/'),
            'create' => Pages\CreateWalkthroughContribution::route('/create'),
            'edit' => Pages\EditWalkthroughContribution::route('/{record}/edit'),
        ];
    }
}
