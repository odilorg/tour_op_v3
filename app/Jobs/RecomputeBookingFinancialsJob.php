<?php

namespace App\Jobs;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RecomputeBookingFinancialsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(private Booking $booking)
    {
    }

    public function handle(): void
    {
        $booking = $this->booking->fresh(['assignments', 'payments']);
        if (! $booking) {
            return;
        }

        $listTotal = $booking->assignments->sum('line_total_minor');
        $costTotal = $booking->assignments->sum('cost_total_minor');
        $profit = $listTotal - $costTotal;

        $booking->updateQuietly([
            'list_total_minor' => $listTotal,
            'cost_total_minor' => $costTotal,
            'profit_minor' => $profit,
        ]);

        $booking->recalculateProgress();
    }
}
