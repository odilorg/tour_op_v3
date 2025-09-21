<?php

namespace App\Filament\Resources\BookingResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;

class StatusLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'statusLogs';

    public static function form(\Filament\Resources\Form $form): \Filament\Resources\Form
    {
        return $form;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('old_status')->label('From')->badge()->formatStateUsing(fn ($state) => $state?->label()),
                Tables\Columns\TextColumn::make('new_status')->label('To')->badge()->formatStateUsing(fn ($state) => $state?->label()),
                Tables\Columns\TextColumn::make('user.name')->label('By'),
                Tables\Columns\TextColumn::make('changed_at')->dateTime(),
                Tables\Columns\TextColumn::make('note'),
            ])
            ->actions([])
            ->headerActions([])
            ->bulkActions([]);
    }
}
