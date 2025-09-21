<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource;
use App\Models\Tour;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Carbon;

class CreateBooking extends CreateRecord
{
    protected static string $resource = BookingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $tour = isset($data['tour_id']) ? Tour::with('days')->find($data['tour_id']) : null;

        if ($tour && empty($data['currency_code'])) {
            $data['currency_code'] = $tour->default_currency_code;
        }

        if ($tour && isset($data['start_date'])) {
            $start = Carbon::parse($data['start_date']);
            $days = max($tour->days->count() - 1, 0);
            $data['end_date'] = $start->copy()->addDays($days)->toDateString();
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->load('tour.days');
        $this->record->generateDaysFromTour();
    }
}
