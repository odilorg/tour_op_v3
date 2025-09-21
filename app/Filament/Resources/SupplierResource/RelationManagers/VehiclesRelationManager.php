<?php

namespace App\Filament\Resources\SupplierResource\RelationManagers;

use App\Enums\VehicleType;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;

class VehiclesRelationManager extends RelationManager
{
    protected static string $relationship = 'vehicles';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->options(collect(VehicleType::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                    ->required(),
                Forms\Components\TextInput::make('plate')->maxLength(20),
                Forms\Components\TextInput::make('seats')->numeric()->minValue(1),
                Forms\Components\Textarea::make('notes')->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')->badge(),
                Tables\Columns\TextColumn::make('plate'),
                Tables\Columns\TextColumn::make('seats'),
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
