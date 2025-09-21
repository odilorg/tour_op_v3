<?php

namespace App\Filament\Resources\TourResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;

class TourDaysRelationManager extends RelationManager
{
    protected static string $relationship = 'days';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('day_index')->numeric()->required()->minValue(1),
                Forms\Components\TextInput::make('title')->required(),
                Forms\Components\Textarea::make('description')->columnSpanFull(),
                Forms\Components\TextInput::make('planned_duration_minutes')->numeric()->minValue(0),
                SpatieMediaLibraryFileUpload::make('image')
                    ->collection('images')
                    ->image()
                    ->disk(config('media-library.disk_name')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('day_index')->sortable(),
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('planned_duration_minutes'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
