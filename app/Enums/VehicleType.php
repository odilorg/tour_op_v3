<?php

namespace App\Enums;

enum VehicleType: string
{
    case SEDAN = 'sedan';
    case MINIVAN = 'minivan';
    case BUS = 'bus';
    case SUV = 'suv';

    public function label(): string
    {
        return match ($this) {
            self::SEDAN => 'Sedan',
            self::MINIVAN => 'Minivan',
            self::BUS => 'Bus',
            self::SUV => 'SUV',
        };
    }
}
