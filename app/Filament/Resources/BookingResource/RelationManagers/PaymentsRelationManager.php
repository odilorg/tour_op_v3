<?php

namespace App\Filament\Resources\BookingResource\RelationManagers;

use App\Enums\PaymentDirection;
use App\Models\Booking;
use App\Support\Money;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('direction')
                    ->options(collect(PaymentDirection::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                    ->required(),
                Forms\Components\TextInput::make('method'),
                Forms\Components\TextInput::make('amount_minor')->numeric()->minValue(0)->required(),
                Forms\Components\TextInput::make('currency_code')->maxLength(3)->required(),
                Forms\Components\DateTimePicker::make('paid_at')->required(),
                Forms\Components\TextInput::make('reference'),
                Forms\Components\Textarea::make('notes')->columnSpanFull(),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('direction')->badge()->formatStateUsing(fn (string $state) => PaymentDirection::from($state)->label()),
                Tables\Columns\TextColumn::make('method'),
                Tables\Columns\TextColumn::make('amount_minor')->formatStateUsing(fn ($state, $record) => Money::format($state, $record->currency_code)),
                Tables\Columns\TextColumn::make('paid_at')->dateTime(),
                Tables\Columns\TextColumn::make('reference'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(fn (array $data, Booking $record) => $data + ['currency_code' => $data['currency_code'] ?? $record->currency_code]),
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
