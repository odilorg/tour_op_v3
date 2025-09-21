<?php

namespace App\Enums;

enum SupplierRateUnit: string
{
    case PER_DAY = 'per_day';
    case HALF_DAY = 'half_day';
    case PICKUP = 'pickup';
    case DROPOFF = 'dropoff';
    case PER_KM = 'per_km';
    case PER_NIGHT = 'per_night';
    case PER_MEAL = 'per_meal';
    case PER_TICKET = 'per_ticket';

    public function label(): string
    {
        return match ($this) {
            self::PER_DAY => 'Per Day',
            self::HALF_DAY => 'Half Day',
            self::PICKUP => 'Pickup',
            self::DROPOFF => 'Dropoff',
            self::PER_KM => 'Per Km',
            self::PER_NIGHT => 'Per Night',
            self::PER_MEAL => 'Per Meal',
            self::PER_TICKET => 'Per Ticket',
        };
    }
}
