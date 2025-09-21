<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class RegenerateBookingDaysCommand extends Command
{
    protected $signature = 'bookings:regenerate-days {booking_id}';

    protected $description = 'Regenerate booking days from the tour template.';

    public function handle(): int
    {
        $booking = Booking::with('tour.days')->find($this->argument('booking_id'));

        if (! $booking || ! $booking->tour) {
            $this->error('Booking or tour not found.');

            return self::FAILURE;
        }

        $booking->days()->delete();

        $startDate = $booking->start_date;

        foreach ($booking->tour->days as $day) {
            $booking->days()->create([
                'date' => Carbon::parse($startDate)->addDays($day->day_index - 1),
                'day_index' => $day->day_index,
                'title' => $day->title,
                'description' => $day->description,
            ]);
        }

        $this->info('Booking days regenerated.');

        return self::SUCCESS;
    }
}
