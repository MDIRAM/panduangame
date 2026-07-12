<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\GameResource\Pages;
use App\Filament\Admin\Resources\GameResource\RelationManagers\ChaptersRelationManager;
use App\Models\Game;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GameResource extends Resource
{
    protected static ?string $model = Game::class;

    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static ?string $navigationGroup = 'Walkthrough Content';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Game')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->alphaDash()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                    Forms\Components\TextInput::make('route_slug')
                        ->label('Public URL slug')
                        ->required()
                        ->alphaDash()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                    Forms\Components\TextInput::make('subtitle')
                        ->maxLength(255),
                    Forms\Components\Textarea::make('description')
                        ->rows(4)
                        ->columnSpanFull(),
                    Forms\Components\Select::make('theme_preset')
                        ->label('Walkthrough color theme')
                        ->options([
                            'blue' => 'Blue / modern',
                            'gold' => 'Gold / soulslike',
                            'red' => 'Red',
                            'green' => 'Green',
                            'neutral' => 'Neutral',
                        ])
                        ->default('blue')
                        ->required()
                        ->native(false),
                    Forms\Components\Select::make('content_status')
                        ->label('Walkthrough status')
                        ->helperText('Complete = sudah selesai, Ongoing = masih dikerjakan, Upcoming = belum diisi.')
                        ->options(Game::contentStatuses())
                        ->default('ongoing')
                        ->required()
                        ->native(false),
                    Forms\Components\Toggle::make('comments_enabled')
                        ->label('Enable comments')
                        ->helperText('Kalau mati, komentar di semua chapter game ini disembunyikan dan user tidak bisa submit.')
                        ->default(true),
                    Forms\Components\TagsInput::make('highlights')
                        ->placeholder('Tambahkan highlight')
                        ->columnSpanFull(),
                    Forms\Components\FileUpload::make('cover_image')
                        ->image()
                        ->disk('public')
                        ->directory('coverimg/games')
                        ->visibility('public')
                        ->columnSpanFull(),
                    Forms\Components\Toggle::make('is_featured')
                        ->label('Featured on homepage'),
                    Forms\Components\Toggle::make('is_published')
                        ->label('Published')
                        ->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')
                    ->getStateUsing(fn (Game $record) => $record->cover_url)
                    ->square(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('route_slug')
                    ->label('Public slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('theme_preset')
                    ->label('Theme')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => ucfirst($state ?: 'blue')),
                Tables\Columns\TextColumn::make('content_status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => Game::contentStatuses()[$state] ?? 'Ongoing')
                    ->color(fn (?string $state): string => match ($state) {
                        'complete' => 'success',
                        'upcoming' => 'warning',
                        default => 'info',
                    }),
                Tables\Columns\TextColumn::make('chapters_count')
                    ->counts('chapters')
                    ->label('Chapters')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_published')
                    ->boolean(),
                Tables\Columns\IconColumn::make('comments_enabled')
                    ->label('Comments')
                    ->boolean(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Published'),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Featured'),
                Tables\Filters\SelectFilter::make('content_status')
                    ->label('Walkthrough status')
                    ->options(Game::contentStatuses()),
                Tables\Filters\TernaryFilter::make('comments_enabled')
                    ->label('Comments enabled'),
            ])
            ->actions([
                Tables\Actions\Action::make('manageWalkthrough')
                    ->label('Manage Walkthrough')
                    ->icon('heroicon-o-book-open')
                    ->url(function (Game $record): string {
                        $firstChapter = $record->chapters()->first();

                        if ($firstChapter) {
                            return ChapterResource::getUrl('edit', ['record' => $firstChapter]);
                        }

                        return static::getUrl('edit', ['record' => $record]);
                    }),
                Tables\Actions\EditAction::make()
                    ->label('Game Settings'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ChaptersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGames::route('/'),
            'create' => Pages\CreateGame::route('/create'),
            'edit' => Pages\EditGame::route('/{record}/edit'),
        ];
    }
}
