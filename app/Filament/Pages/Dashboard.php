<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AtRiskBookingsTable;
use App\Filament\Widgets\BookingStatsOverview;
use App\Filament\Widgets\WeeklyScheduleWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            BookingStatsOverview::class,
            AtRiskBookingsTable::class,
            WeeklyScheduleWidget::class,
        ];
    }
}
