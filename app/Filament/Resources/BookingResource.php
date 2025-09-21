<?php

namespace App\Filament\Resources;

use App\Enums\BookingStatus;
use App\Filament\Resources\BookingResource\Pages;
use App\Filament\Resources\BookingResource\RelationManagers\AssignmentsRelationManager;
use App\Filament\Resources\BookingResource\RelationManagers\BookingDaysRelationManager;
use App\Filament\Resources\BookingResource\RelationManagers\PaymentsRelationManager;
use App\Filament\Resources\BookingResource\RelationManagers\StatusLogsRelationManager;
use App\Models\Booking;
use App\Support\Money;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'Operations';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Booking Details')
                    ->schema([
                        Forms\Components\Select::make('tour_id')
                            ->relationship('tour', 'title')
                            ->searchable()
                            ->nullable(),
                        Forms\Components\TextInput::make('reference_code')->required()->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('customer_name')->required(),
                        Forms\Components\TextInput::make('customer_phone'),
                        Forms\Components\TextInput::make('customer_email')->email(),
                        Forms\Components\DatePicker::make('start_date')->required(),
                        Forms\Components\DatePicker::make('end_date')->required()->afterOrEqual('start_date'),
                        Forms\Components\TextInput::make('party_size')->numeric()->minValue(1)->required(),
                        Forms\Components\Select::make('operator_id')
                            ->relationship('operator', 'name')
                            ->searchable(),
                        Forms\Components\Select::make('status')
                            ->options(collect(BookingStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                            ->required(),
                        Forms\Components\TextInput::make('currency_code')
                            ->label('Currency')
                            ->required(),
                    ])->columns(3),
                Forms\Components\Section::make('Financial Summary')
                    ->schema([
                        Forms\Components\TextInput::make('markup_percent')->numeric()->minValue(0),
                        Forms\Components\TextInput::make('list_total_minor')->disabled()->dehydrated(false)->formatStateUsing(fn ($state, $record) => $record ? Money::format($record->list_total_minor, $record->currency_code) : '0'),
                        Forms\Components\TextInput::make('cost_total_minor')->disabled()->dehydrated(false)->formatStateUsing(fn ($state, $record) => $record ? Money::format($record->cost_total_minor, $record->currency_code) : '0'),
                        Forms\Components\TextInput::make('profit_minor')->disabled()->dehydrated(false)->formatStateUsing(fn ($state, $record) => $record ? Money::format($record->profit_minor, $record->currency_code) : '0'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference_code')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('customer_name')->searchable(),
                Tables\Columns\TextColumn::make('start_date')->date(),
                Tables\Columns\TextColumn::make('end_date')->date(),
                Tables\Columns\TextColumn::make('party_size'),
                Tables\Columns\BadgeColumn::make('status')->colors([
                    'warning' => BookingStatus::REQUESTED->value,
                    'success' => BookingStatus::COMPLETED->value,
                    'danger' => BookingStatus::CANCELLED->value,
                    'info' => BookingStatus::CONFIRMED->value,
                ])->formatStateUsing(fn (string $state) => BookingStatus::from($state)->label()),
                Tables\Columns\ProgressColumn::make('progress_percent')
                    ->label('Progress')
                    ->color(function ($state) {
                        return $state >= 90 ? 'success' : ($state >= 60 ? 'warning' : 'danger');
                    }),
                Tables\Columns\TextColumn::make('list_total_minor')
                    ->label('Revenue')
                    ->formatStateUsing(fn ($state, $record) => Money::format($state, $record->currency_code))
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(collect(BookingStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])),
                Tables\Filters\SelectFilter::make('operator_id')
                    ->relationship('operator', 'name'),
            ])
            ->actions([
                Tables\Actions\Action::make('requestConfirmation')
                    ->label('Request Confirmation')
                    ->action(fn (Booking $record) => $record->statusLogs()->create([
                        'old_status' => $record->status->value,
                        'new_status' => $record->status->value,
                        'changed_at' => now(),
                    ])),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            BookingDaysRelationManager::class,
            AssignmentsRelationManager::class,
            PaymentsRelationManager::class,
            StatusLogsRelationManager::class,
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['reference_code', 'customer_name', 'start_date'];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }
}
