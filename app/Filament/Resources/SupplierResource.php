<?php

namespace App\Filament\Resources;

use App\Enums\SupplierType;
use App\Filament\Resources\SupplierResource\Pages;
use App\Filament\Resources\SupplierResource\RelationManagers\RatesRelationManager;
use App\Filament\Resources\SupplierResource\RelationManagers\VehiclesRelationManager;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Operations';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->options(collect(SupplierType::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                            ->required(),
                        Forms\Components\Toggle::make('is_active')->default(true),
                        Forms\Components\TextInput::make('name')
                            ->columnSpanFull()
                            ->required(),
                        Forms\Components\TextInput::make('contact_name'),
                        Forms\Components\TextInput::make('phone'),
                        Forms\Components\TextInput::make('email')
                            ->email(),
                    ]),
                Forms\Components\Textarea::make('notes')->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\BadgeColumn::make('type')
                    ->colors([
                        'primary',
                    ])
                    ->formatStateUsing(fn (string $state) => \App\Enums\SupplierType::from($state)->label())
                    ->label('Type'),
                Tables\Columns\TextColumn::make('contact_name'),
                Tables\Columns\TextColumn::make('phone'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(collect(SupplierType::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])),
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            VehiclesRelationManager::class,
            RatesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }
}
