<?php

namespace App\Filament\Resources\BookingResource\RelationManagers;

use App\Enums\BookingAssignmentRole;
use App\Enums\BookingAssignmentStatus;
use App\Enums\SupplierRateServiceType;
use App\Enums\SupplierRateUnit;
use App\Enums\VehicleType;
use App\Models\Booking;
use App\Models\SupplierRate;
use App\Support\Money;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;

class AssignmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'assignments';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('booking_day_id')
                    ->relationship('bookingDay', 'title')
                    ->required(),
                Forms\Components\Select::make('supplier_id')
                    ->relationship('supplier', 'name')
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn (callable $set, $state) => self::setDefaultRole($set, $state)),
                Forms\Components\Select::make('vehicle_id')
                    ->relationship('vehicle', 'plate')
                    ->searchable(),
                Forms\Components\Select::make('role')
                    ->options(collect(BookingAssignmentRole::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                    ->required(),
                Forms\Components\Select::make('service_type')
                    ->options(collect(SupplierRateServiceType::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])),
                Forms\Components\Select::make('unit')
                    ->options(collect(SupplierRateUnit::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])),
                Forms\Components\Select::make('vehicle_type')
                    ->options(collect(VehicleType::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                    ->label('Vehicle Type'),
                Forms\Components\TextInput::make('qty')->numeric()->minValue(1)->default(1),
                Forms\Components\TextInput::make('rate_minor')->numeric()->minValue(0)->required(),
                Forms\Components\TextInput::make('cost_minor')->numeric()->minValue(0),
                Forms\Components\TextInput::make('currency_code')->maxLength(3)->required(),
                Forms\Components\Select::make('status')
                    ->options(collect(BookingAssignmentStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                    ->required(),
                Forms\Components\Textarea::make('notes')->columnSpanFull(),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('bookingDay.date')->label('Date')->date(),
                Tables\Columns\TextColumn::make('supplier.name')->label('Supplier')->searchable(),
                Tables\Columns\TextColumn::make('role')->badge(),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('rate_minor')->label('Rate')->formatStateUsing(fn ($state, $record) => Money::format($state, $record->currency_code)),
                Tables\Columns\TextColumn::make('qty'),
                Tables\Columns\TextColumn::make('line_total_minor')->label('Total')->formatStateUsing(fn ($state, $record) => Money::format($state, $record->currency_code)),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(fn (array $data, Booking $record) => $data + [
                        'booking_id' => $record->id,
                        'currency_code' => $data['currency_code'] ?? $record->currency_code,
                    ]),
                Tables\Actions\Action::make('addFromRate')
                    ->label('Pick from Supplier Rates')
                    ->form([
                        Forms\Components\Select::make('booking_day_id')
                            ->label('Booking Day')
                            ->options(fn (self $livewire) => $livewire->ownerRecord->days()->orderBy('day_index')->pluck('title', 'id'))
                            ->required(),
                        Forms\Components\Select::make('supplier_rate_id')
                            ->label('Supplier Rate')
                            ->options(fn () => SupplierRate::query()->where('is_active', true)->with('supplier')->get()->mapWithKeys(fn ($rate) => [
                                $rate->id => $rate->supplier->name . ' - ' . $rate->service_type->label() . ' (' . $rate->currency_code . ')',
                            ]))
                            ->searchable()
                            ->required(),
                        Forms\Components\TextInput::make('qty')->numeric()->default(1)->minValue(1),
                    ])
                    ->action(function (array $data, Booking $record) {
                        $rate = SupplierRate::with('supplier')->findOrFail($data['supplier_rate_id']);
                        $role = self::guessRoleFromSupplierType($rate->supplier->type->value);

                        $record->assignments()->create([
                            'booking_day_id' => $data['booking_day_id'],
                            'supplier_id' => $rate->supplier_id,
                            'booking_id' => $record->id,
                            'role' => $role->value,
                            'service_type' => $rate->service_type->value,
                            'unit' => $rate->unit->value,
                            'vehicle_type' => $rate->vehicle_type?->value,
                            'qty' => $data['qty'],
                            'rate_minor' => $rate->amount_minor,
                            'cost_minor' => $rate->amount_minor,
                            'currency_code' => $rate->currency_code,
                            'status' => BookingAssignmentStatus::PENDING->value,
                        ]);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('markConfirmed')
                    ->label('Mark as Confirmed')
                    ->icon('heroicon-o-check')
                    ->visible(fn ($record) => $record->status !== BookingAssignmentStatus::COMPANY_CONFIRMED)
                    ->action(fn ($record) => $record->update([
                        'status' => BookingAssignmentStatus::COMPANY_CONFIRMED,
                        'confirmed_at' => now(),
                        'confirmed_by' => auth()->id(),
                    ])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    protected static function setDefaultRole(callable $set, ?int $supplierId): void
    {
        if (! $supplierId) {
            return;
        }

        $supplier = \App\Models\Supplier::find($supplierId);

        if (! $supplier) {
            return;
        }

        $set('role', self::guessRoleFromSupplierType($supplier->type->value)->value);
    }

    protected static function guessRoleFromSupplierType(string $type): BookingAssignmentRole
    {
        return match ($type) {
            'guide' => BookingAssignmentRole::GUIDE,
            'driver', 'transport_company' => BookingAssignmentRole::TRANSPORT,
            'hotel' => BookingAssignmentRole::HOTEL,
            'restaurant' => BookingAssignmentRole::RESTAURANT,
            default => BookingAssignmentRole::ATTRACTION,
        };
    }
}
