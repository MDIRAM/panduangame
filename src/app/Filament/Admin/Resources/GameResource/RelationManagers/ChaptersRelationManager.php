<?php

namespace App\Filament\Admin\Resources\GameResource\RelationManagers;

use App\Filament\Admin\Resources\ChapterResource;
use App\Models\Chapter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Unique;

class ChaptersRelationManager extends RelationManager
{
    protected static string $relationship = 'chapters';

    protected static ?string $title = 'Walkthrough Chapters';

    public function form(Form $form): Form
    {
        return $form
            ->columns(2)
            ->schema([
                Forms\Components\Select::make('parent_id')
                    ->label('Parent chapter')
                    ->options(fn (?Chapter $record): array => $this->getOwnerRecord()
                        ->chapters()
                        ->when($record, fn ($query) => $query->whereKeyNot($record->getKey()))
                        ->orderBy('order')
                        ->pluck('chapter_title', 'id')
                        ->all())
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Forms\Components\TextInput::make('order')
                    ->numeric()
                    ->minValue(0)
                    ->required()
                    ->default(fn (): int => ((int) $this->getOwnerRecord()->chapters()->max('order')) + 1),
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
                        modifyRuleUsing: fn (Unique $rule) => $rule
                            ->where('game_id', $this->getOwnerRecord()->id),
                    ),
                Forms\Components\TextInput::make('section_title')
                    ->default(fn (): string => $this->getOwnerRecord()
                        ->chapters()
                        ->whereNotNull('section_title')
                        ->orderBy('order')
                        ->value('section_title') ?: 'Main Walkthrough')
                    ->maxLength(255),
                Forms\Components\TextInput::make('source_url')
                    ->label('Reference source URL')
                    ->helperText('Contoh: halaman area dari Fextralife yang dipakai sebagai referensi.')
                    ->url()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\TagsInput::make('overview')
                    ->label('Overview paragraphs')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('overview_image')
                    ->label('Overview image')
                    ->image()
                    ->imageEditor()
                    ->disk('public')
                    ->directory('walkthrough/chapters/overview')
                    ->visibility('public')
                    ->maxSize(5120),
                Forms\Components\FileUpload::make('cover_image')
                    ->label('Chapter card image')
                    ->image()
                    ->imageEditor()
                    ->disk('public')
                    ->directory('walkthrough/chapters/covers')
                    ->visibility('public')
                    ->maxSize(5120),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('chapter_title')
            ->defaultSort('order')
            ->reorderable('order')
            ->columns([
                Tables\Columns\TextColumn::make('order')
                    ->sortable(),
                Tables\Columns\TextColumn::make('chapter_title')
                    ->label('Chapter / Area')
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('parent.chapter_title')
                    ->label('Parent')
                    ->placeholder('Top level')
                    ->wrap(),
                Tables\Columns\TextColumn::make('steps_count')
                    ->counts('steps')
                    ->label('Steps'),
                Tables\Columns\IconColumn::make('source_url')
                    ->label('Source')
                    ->boolean()
                    ->getStateUsing(fn (Chapter $record): bool => filled($record->source_url)),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Chapter'),
            ])
            ->actions([
                Tables\Actions\Action::make('manageSteps')
                    ->label('Isi Walkthrough')
                    ->icon('heroicon-o-list-bullet')
                    ->url(fn (Chapter $record): string => ChapterResource::getUrl('edit', ['record' => $record])),
                Tables\Actions\Action::make('openSource')
                    ->label('Source')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Chapter $record): ?string => $record->source_url)
                    ->openUrlInNewTab()
                    ->visible(fn (Chapter $record): bool => filled($record->source_url)),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
