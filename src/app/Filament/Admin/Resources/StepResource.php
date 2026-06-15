<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\StepResource\Pages;
use App\Models\Step;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StepResource extends Resource
{
    protected static ?string $model = Step::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?string $navigationGroup = 'Walkthrough Content';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Walkthrough Step')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('chapter_id')
                        ->relationship(
                            name: 'chapter',
                            titleAttribute: 'chapter_title',
                            modifyQueryUsing: fn ($query) => $query->with('game'),
                        )
                        ->getOptionLabelFromRecordUsing(
                            fn ($record) => $record->game->title.' - '.$record->chapter_title,
                        )
                        ->searchable(['chapter_title'])
                        ->preload()
                        ->required(),
                    Forms\Components\TextInput::make('order')
                        ->numeric()
                        ->minValue(0)
                        ->required()
                        ->default(0),
                    Forms\Components\TextInput::make('step_title')
                        ->maxLength(255)
                        ->columnSpanFull(),
                    RichEditor::make('content')
                        ->required()
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('image_url')
                        ->label('Image path or URL')
                        ->maxLength(255)
                        ->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('chapter.game.title')
                    ->label('Game')
                    ->searchable(),
                Tables\Columns\TextColumn::make('chapter.chapter_title')
                    ->label('Chapter')
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('step_title')
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('content')
                    ->formatStateUsing(fn (?string $state): string => strip_tags($state ?? ''))
                    ->limit(80)
                    ->wrap(),
                Tables\Columns\IconColumn::make('image_url')
                    ->label('Image')
                    ->getStateUsing(fn (Step $record): bool => filled($record->image_url))
                    ->boolean(),
            ])
            ->filters([
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
            'index' => Pages\ListSteps::route('/'),
            'create' => Pages\CreateStep::route('/create'),
            'edit' => Pages\EditStep::route('/{record}/edit'),
        ];
    }
}
