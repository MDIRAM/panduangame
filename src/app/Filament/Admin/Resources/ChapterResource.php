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

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $recordTitleAttribute = 'chapter_title';

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'Walkthrough Content';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Sidebar Page')
                ->description('Atur nama halaman yang tampil di sidebar dan posisinya.')
                ->columns(3)
                ->schema([
                    Forms\Components\Select::make('game_id')
                        ->relationship('game', 'title')
                        ->default(fn (): ?int => request()->integer('game_id') ?: null)
                        ->searchable()
                        ->preload()
                        ->live()
                        ->required()
                        ->hidden(fn ($livewire): bool => $livewire instanceof Pages\EditChapter),
                    Forms\Components\TextInput::make('chapter_title')
                        ->label('Sidebar title')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(fn ($livewire): int => $livewire instanceof Pages\EditChapter ? 2 : 3),
                    Forms\Components\Select::make('parent_id')
                        ->label('Place under')
                        ->placeholder('Top-level sidebar page')
                        ->options(fn (Forms\Get $get, ?Chapter $record): array => Chapter::query()
                            ->where('game_id', $get('game_id'))
                            ->whereNull('parent_id')
                            ->when($record, fn ($query) => $query->whereKeyNot($record->getKey()))
                            ->orderBy('order')
                            ->pluck('chapter_title', 'id')
                            ->all())
                        ->searchable()
                        ->preload()
                        ->nullable(),
                    Forms\Components\TextInput::make('order')
                        ->label('Sidebar order')
                        ->numeric()
                        ->minValue(0)
                        ->required()
                        ->default(fn (): int => request()->integer('game_id')
                            ? ((int) Chapter::where('game_id', request()->integer('game_id'))->max('order')) + 1
                            : 1),
                ]),
            Forms\Components\Section::make('Walkthrough Document')
                ->description('Tulis atau paste seluruh walkthrough di sini. Format dari situs lain akan dirapikan otomatis.')
                ->schema([
                    Forms\Components\RichEditor::make('document_content')
                        ->label('Walkthrough content')
                        ->fileAttachmentsDisk('public')
                        ->fileAttachmentsDirectory('walkthrough/content')
                        ->fileAttachmentsVisibility('public')
                        ->getUploadedAttachmentUrlUsing(
                            fn (string $file): string => '/storage/' . ltrim($file, '/'),
                        )
                        ->toolbarButtons([
                            'attachFiles',
                            'blockquote',
                            'bold',
                            'bulletList',
                            'h2',
                            'h3',
                            'italic',
                            'link',
                            'orderedList',
                            'redo',
                            'strike',
                            'underline',
                            'undo',
                        ])
                        ->extraAttributes(['class' => 'walkthrough-document-editor'])
                        ->columnSpanFull(),
                ])
                ->visible(fn ($livewire): bool => $livewire instanceof Pages\EditChapter),
            Forms\Components\Section::make('Advanced Settings')
                ->description('Slug, sumber referensi, ringkasan lama, dan gambar cover.')
                ->collapsible()
                ->collapsed()
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('slug')
                        ->helperText('Dibuat otomatis dari judul saat menambah halaman baru.')
                        ->required(fn ($livewire): bool => $livewire instanceof Pages\EditChapter)
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
                        ->label('Reference source URL')
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
                        ->maxSize(5120)
                        ->openable()
                        ->downloadable(),
                    Forms\Components\FileUpload::make('cover_image')
                        ->label('Chapter card image')
                        ->image()
                        ->imageEditor()
                        ->disk('public')
                        ->directory('walkthrough/chapters/covers')
                        ->visibility('public')
                        ->maxSize(5120)
                        ->openable()
                        ->downloadable(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('order')
            ->reorderable('order')
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')
                    ->label('Image')
                    ->getStateUsing(fn (Chapter $record) => $record->cover_url ?? $record->overview_image_url)
                    ->square(),
                Tables\Columns\TextColumn::make('order')
                    ->sortable(),
                Tables\Columns\TextColumn::make('game.title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('chapter_title')
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('parent.chapter_title')
                    ->label('Parent')
                    ->placeholder('Top level')
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
