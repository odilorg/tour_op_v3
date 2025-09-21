<?php

namespace App\Models;

use App\Enums\BookingAssignmentRole;
use App\Enums\BookingAssignmentStatus;
use App\Enums\SupplierRateServiceType;
use App\Enums\SupplierRateUnit;
use App\Enums\VehicleType;
use App\Models\User;
use App\Notifications\AssignmentStatusChanged;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class BookingAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'booking_day_id',
        'supplier_id',
        'vehicle_id',
        'role',
        'service_type',
        'unit',
        'vehicle_type',
        'qty',
        'rate_minor',
        'cost_minor',
        'line_total_minor',
        'cost_total_minor',
        'currency_code',
        'status',
        'notes',
        'confirmed_at',
        'confirmed_by',
    ];

    protected $casts = [
        'role' => BookingAssignmentRole::class,
        'service_type' => SupplierRateServiceType::class,
        'unit' => SupplierRateUnit::class,
        'vehicle_type' => VehicleType::class,
        'status' => BookingAssignmentStatus::class,
        'confirmed_at' => 'datetime',
    ];

    public function bookingDay()
    {
        return $this->belongsTo(BookingDay::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function confirmer()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    protected static function booted(): void
    {
        static::saving(function (self $assignment) {
            if (! $assignment->booking_id && $assignment->bookingDay) {
                $assignment->booking_id = $assignment->bookingDay->booking_id;
            }

            $costRate = $assignment->cost_minor ?? $assignment->rate_minor;
            $assignment->cost_minor = $costRate;
            $assignment->line_total_minor = $assignment->qty * $assignment->rate_minor;
            $assignment->cost_total_minor = $assignment->qty * $costRate;

            if ($assignment->vehicle_id) {
                $conflict = self::query()
                    ->where('vehicle_id', $assignment->vehicle_id)
                    ->where('booking_day_id', $assignment->booking_day_id)
                    ->when($assignment->exists, fn ($query) => $query->where('id', '!=', $assignment->id))
                    ->exists();

                if ($conflict) {
                    throw ValidationException::withMessages([
                        'vehicle_id' => 'This vehicle is already assigned for the selected day.',
                    ]);
                }
            }
        });

        static::saved(function (self $assignment) {
            $assignment->loadMissing('booking', 'bookingDay');

            if ($assignment->booking) {
                $assignment->booking->recalculateProgress();
            }

            if ($assignment->wasChanged('status') && $assignment->status === BookingAssignmentStatus::SUPPLIER_CONFIRMED) {
                $operator = $assignment->booking?->operator;
                $managers = User::role('manager')->get();
                $notifiables = collect([$operator])->filter()->merge($managers);

                if ($notifiables->isNotEmpty()) {
                    Notification::send($notifiables, new AssignmentStatusChanged($assignment));
                }
            }
        });
    }
}
