<?php

namespace App\Filament\Admin\Resources\ChapterResource\RelationManagers;

use App\Models\Step;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class StepsRelationManager extends RelationManager
{
    protected static string $relationship = 'steps';

    protected static ?string $title = 'Walkthrough Steps';

    public function form(Form $form): Form
    {
        return $form
            ->columns(2)
            ->schema([
                Forms\Components\TextInput::make('order')
                    ->numeric()
                    ->minValue(0)
                    ->required()
                    ->default(fn (): int => ((int) $this->getOwnerRecord()->steps()->max('order')) + 1),
                Forms\Components\TextInput::make('step_title')
                    ->label('Step title')
                    ->required()
                    ->maxLength(255),
                RichEditor::make('content')
                    ->label('Walkthrough content')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('image_url')
                    ->label('Supporting image')
                    ->image()
                    ->imageEditor()
                    ->disk('public')
                    ->directory('walkthrough/steps')
                    ->visibility('public')
                    ->maxSize(5120)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('step_title')
            ->defaultSort('order')
            ->reorderable('order')
            ->columns([
                Tables\Columns\TextColumn::make('order')
                    ->sortable(),
                Tables\Columns\ImageColumn::make('image_url')
                    ->label('Image')
                    ->getStateUsing(fn (Step $record) => $record->resolved_image_url)
                    ->square(),
                Tables\Columns\TextColumn::make('step_title')
                    ->label('Step')
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('content')
                    ->formatStateUsing(fn (?string $state): string => strip_tags($state ?? ''))
                    ->limit(90)
                    ->wrap(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Step'),
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
}
