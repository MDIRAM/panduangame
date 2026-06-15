<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ChapterResource\Pages;
use App\Models\Chapter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Unique;

class ChapterResource extends Resource
{
    protected static ?string $model = Chapter::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'Walkthrough Content';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Chapter')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('game_id')
                        ->relationship('game', 'title')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\TextInput::make('order')
                        ->numeric()
                        ->minValue(0)
                        ->required()
                        ->default(0),
                    Forms\Components\TextInput::make('chapter_title')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->alphaDash()
                        ->maxLength(255)
                        ->unique(
                            table: Chapter::class,
                            column: 'slug',
                            ignoreRecord: true,
                            modifyRuleUsing: fn (Unique $rule, Forms\Get $get) => $rule
                                ->where('game_id', $get('game_id')),
                        ),
                    Forms\Components\TextInput::make('section_title')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('source_url')
                        ->url()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Forms\Components\TagsInput::make('overview')
                        ->label('Overview paragraphs')
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('overview_image')
                        ->label('Overview image path or URL')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('cover_image')
                        ->label('Card image path or URL')
                        ->maxLength(255),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('order')
            ->reorderable('order')
            ->columns([
                Tables\Columns\TextColumn::make('order')
                    ->sortable(),
                Tables\Columns\TextColumn::make('game.title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('chapter_title')
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('section_title')
                    ->badge(),
                Tables\Columns\TextColumn::make('steps_count')
                    ->counts('steps')
                    ->label('Steps')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
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
            'index' => Pages\ListChapters::route('/'),
            'create' => Pages\CreateChapter::route('/create'),
            'edit' => Pages\EditChapter::route('/{record}/edit'),
        ];
    }
}
