<?php

namespace App\Models;

use App\Enums\BookingAssignmentStatus;
use App\Enums\BookingStatus;
use App\Jobs\RecomputeBookingFinancialsJob;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use function activity;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'tour_id',
        'operator_id',
        'reference_code',
        'customer_name',
        'customer_phone',
        'customer_email',
        'start_date',
        'end_date',
        'party_size',
        'status',
        'markup_percent',
        'list_total_minor',
        'cost_total_minor',
        'profit_minor',
        'progress_percent',
        'currency_code',
        'manual_status_override',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'status' => BookingStatus::class,
        'manual_status_override' => 'bool',
    ];

    protected static function booted(): void
    {
        static::saved(function (self $booking) {
            if ($booking->wasChanged(['markup_percent'])) {
                RecomputeBookingFinancialsJob::dispatch($booking);
            }
        });

        static::updated(function (self $booking) {
            if ($booking->wasChanged('status')) {
                $oldStatus = $booking->getOriginal('status');
                $newStatus = $booking->status->value;

                $booking->statusLogs()->create([
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'user_id' => Auth::id(),
                    'changed_at' => now(),
                ]);

                activity()
                    ->performedOn($booking)
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'old_status' => $oldStatus,
                        'new_status' => $newStatus,
                    ])
                    ->log('booking-status-updated');
            }
        });
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_code', 'code');
    }

    public function days(): HasMany
    {
        return $this->hasMany(BookingDay::class)->orderBy('day_index');
    }

    public function assignments()
    {
        return $this->hasMany(BookingAssignment::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(BookingPayment::class);
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(BookingStatusLog::class);
    }

    public function scopeUpcoming($query)
    {
        return $query->whereDate('start_date', '>=', now()->startOfDay());
    }

    public function scopeAtRisk($query)
    {
        return $query->whereBetween('start_date', [now()->startOfDay(), now()->addDays(3)])
            ->where('progress_percent', '<', 70)
            ->whereNotIn('status', [BookingStatus::COMPLETED->value, BookingStatus::CANCELLED->value]);
    }

    public function recalculateProgress(): void
    {
        $assignments = $this->assignments;
        $total = $assignments->count();

        if ($total === 0) {
            $this->progress_percent = 0;
            $this->saveQuietly();

            return;
        }

        $completed = $assignments->whereIn('status', [
            BookingAssignmentStatus::SUPPLIER_CONFIRMED->value,
            BookingAssignmentStatus::COMPANY_CONFIRMED->value,
            BookingAssignmentStatus::FULFILLED->value,
        ])->count();

        $progress = (int) round(($completed / $total) * 100);
        $this->progress_percent = $progress;
        $this->saveQuietly();

        $this->refresh();
        $this->autoUpdateStatus();
    }

    public function autoUpdateStatus(): void
    {
        if ($this->manual_status_override) {
            return;
        }

        $currentStatus = $this->status;
        $newStatus = $currentStatus;

        $assignments = $this->assignments;
        $hasConfirmed = $assignments->whereIn('status', [
            BookingAssignmentStatus::SUPPLIER_CONFIRMED->value,
            BookingAssignmentStatus::COMPANY_CONFIRMED->value,
        ])->isNotEmpty();
        $allFulfilled = $assignments->count() > 0 && $assignments->every(fn ($assignment) => $assignment->status === BookingAssignmentStatus::FULFILLED->value);

        if ($allFulfilled) {
            $newStatus = BookingStatus::COMPLETED;
        } elseif ($hasConfirmed && $this->start_date->isPast()) {
            $newStatus = BookingStatus::IN_PROGRESS;
        } elseif ($hasConfirmed) {
            $newStatus = BookingStatus::CONFIRMED;
        }

        if ($newStatus !== $currentStatus) {
            $this->status = $newStatus;
            $this->saveQuietly();
        }
    }

    public function generateDaysFromTour(): void
    {
        if (! $this->tour) {
            return;
        }

        $this->days()->delete();

        foreach ($this->tour->days as $day) {
            $this->days()->create([
                'date' => $this->start_date->copy()->addDays($day->day_index - 1),
                'day_index' => $day->day_index,
                'title' => $day->title,
                'description' => $day->description,
            ]);
        }

        $this->updateQuietly([
            'end_date' => $this->start_date->copy()->addDays(max($this->tour->days->count() - 1, 0)),
        ]);
    }
}
