<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;

class AtRiskBookingsTable extends BaseWidget
{
    protected function getTableQuery()
    {
        return Booking::query()->atRisk()->with('operator');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('reference_code')->label('Reference')->searchable(),
            Tables\Columns\TextColumn::make('customer_name'),
            Tables\Columns\TextColumn::make('start_date')->date(),
            Tables\Columns\TextColumn::make('progress_percent')->suffix('%')->color(fn ($state) => $state >= 70 ? 'success' : 'danger'),
            Tables\Columns\TextColumn::make('operator.name')->label('Operator'),
        ];
    }
}
