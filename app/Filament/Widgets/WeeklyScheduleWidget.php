<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;

class WeeklyScheduleWidget extends Widget
{
    protected static string $view = 'filament.widgets.weekly-schedule';

    protected function getViewData(): array
    {
        $start = Carbon::now()->startOfWeek();
        $end = Carbon::now()->endOfWeek();

        $bookings = Booking::with(['days' => fn ($query) => $query->whereBetween('date', [$start, $end])])->whereBetween('start_date', [$start->copy()->subWeek(), $end])->get();

        return [
            'bookings' => $bookings,
            'start' => $start,
            'end' => $end,
        ];
    }
}
