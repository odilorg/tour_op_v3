<?php

namespace App\Enums;

enum SupplierType: string
{
    case DRIVER = 'driver';
    case TRANSPORT_COMPANY = 'transport_company';
    case GUIDE = 'guide';
    case HOTEL = 'hotel';
    case RESTAURANT = 'restaurant';
    case ATTRACTION = 'attraction';

    public function label(): string
    {
        return match ($this) {
            self::DRIVER => 'Driver',
            self::TRANSPORT_COMPANY => 'Transport Company',
            self::GUIDE => 'Guide',
            self::HOTEL => 'Hotel',
            self::RESTAURANT => 'Restaurant',
            self::ATTRACTION => 'Attraction',
        };
    }
}
