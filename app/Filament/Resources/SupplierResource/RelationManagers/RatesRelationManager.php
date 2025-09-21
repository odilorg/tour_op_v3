<?php

namespace App\Filament\Resources\SupplierResource\RelationManagers;

use App\Enums\SupplierRateServiceType;
use App\Enums\SupplierRateUnit;
use App\Enums\VehicleType;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;

class RatesRelationManager extends RelationManager
{
    protected static string $relationship = 'rates';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('service_type')
                    ->options(collect(SupplierRateServiceType::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                    ->required(),
                Forms\Components\Select::make('unit')
                    ->options(collect(SupplierRateUnit::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                    ->required(),
                Forms\Components\Select::make('vehicle_type')
                    ->options(collect(VehicleType::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                    ->label('Vehicle Type')
                    ->native(false)
                    ->searchable(),
                Forms\Components\TextInput::make('amount_minor')
                    ->label('Amount (minor units)')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('currency_code')
                    ->label('Currency')
                    ->maxLength(3)
                    ->required(),
                Forms\Components\TextInput::make('description'),
                Forms\Components\Toggle::make('is_active')->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('service_type')->badge(),
                Tables\Columns\TextColumn::make('unit')->badge(),
                Tables\Columns\TextColumn::make('vehicle_type')->badge(),
                Tables\Columns\TextColumn::make('amount_minor')->label('Amount'),
                Tables\Columns\TextColumn::make('currency_code'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
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
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('service_type')
                    ->options(collect(SupplierRateServiceType::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])),
            ]);
    }
}
