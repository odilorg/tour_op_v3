<?php

namespace App\Filament\Widgets;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\BookingPayment;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Carbon;

class BookingStatsOverview extends StatsOverviewWidget
{
    protected function getCards(): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();

        $bookingsRequested = Booking::whereBetween('start_date', [$startOfMonth, now()])
            ->where('status', BookingStatus::REQUESTED->value)
            ->count();

        $revenue = BookingPayment::where('direction', 'incoming')->sum('amount_minor');
        $costs = BookingPayment::where('direction', 'outgoing')->sum('amount_minor');
        $averageMargin = Booking::where('list_total_minor', '>', 0)->average('profit_minor') ?? 0;

        return [
            Card::make('Bookings Requested (Month)', (string) $bookingsRequested),
            Card::make('Total Revenue', number_format($revenue / 100, 2))->description('All incoming payments'),
            Card::make('Total Cost', number_format($costs / 100, 2))->description('All outgoing payments'),
            Card::make('Average Profit (minor)', number_format((float) $averageMargin, 2)),
        ];
    }
}
