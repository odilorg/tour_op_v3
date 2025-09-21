<?php

namespace App\Enums;

enum BookingAssignmentRole: string
{
    case GUIDE = 'guide';
    case TRANSPORT = 'transport';
    case HOTEL = 'hotel';
    case RESTAURANT = 'restaurant';
    case ATTRACTION = 'attraction';

    public function label(): string
    {
        return match ($this) {
            self::GUIDE => 'Guide',
            self::TRANSPORT => 'Transport',
            self::HOTEL => 'Hotel',
            self::RESTAURANT => 'Restaurant',
            self::ATTRACTION => 'Attraction',
        };
    }
}
