<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ChapterCommentResource\Pages;
use App\Models\ChapterComment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ChapterCommentResource extends Resource
{
    protected static ?string $model = ChapterComment::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Community';

    protected static ?string $navigationLabel = 'Comments';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Comment')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('chapter_id')
                        ->relationship(
                            name: 'chapter',
                            titleAttribute: 'chapter_title',
                            modifyQueryUsing: fn ($query) => $query->with('game'),
                        )
                        ->getOptionLabelFromRecordUsing(
                            fn ($record) => $record->game->title . ' - ' . $record->chapter_title,
                        )
                        ->searchable(['chapter_title'])
                        ->preload()
                        ->required(),
                    Forms\Components\Select::make('user_id')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\Toggle::make('is_approved')
                        ->label('Visible on website')
                        ->default(true)
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('body')
                        ->label('Comment text')
                        ->required()
                        ->maxLength(2000)
                        ->rows(5)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('chapter.game.title')
                    ->label('Game')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('chapter.chapter_title')
                    ->label('Chapter')
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('body')
                    ->label('Comment')
                    ->limit(90)
                    ->wrap()
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_approved')
                    ->label('Visible')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_approved')
                    ->label('Visible on website'),
                Tables\Filters\SelectFilter::make('chapter_id')
                    ->relationship('chapter', 'chapter_title')
                    ->searchable()
                    ->preload(),
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
            'index' => Pages\ListChapterComments::route('/'),
            'create' => Pages\CreateChapterComment::route('/create'),
            'edit' => Pages\EditChapterComment::route('/{record}/edit'),
        ];
    }
}
