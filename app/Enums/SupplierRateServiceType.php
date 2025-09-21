<?php

namespace App\Enums;

enum SupplierRateServiceType: string
{
    case GUIDE_FULL_DAY = 'guide_full_day';
    case GUIDE_HALF_DAY = 'guide_half_day';
    case GUIDE_HOURLY = 'guide_hourly';
    case TRANSPORT_PICKUP = 'transport_pickup';
    case TRANSPORT_DROPOFF = 'transport_dropoff';
    case TRANSPORT_FULL_DAY = 'transport_full_day';
    case TRANSPORT_HALF_DAY = 'transport_half_day';
    case TRANSPORT_PER_KM = 'transport_per_km';
    case HOTEL_ROOM_NIGHT_STANDARD = 'hotel_room_night_standard';
    case HOTEL_ROOM_NIGHT_DELUXE = 'hotel_room_night_deluxe';
    case HOTEL_BREAKFAST = 'hotel_breakfast';
    case HOTEL_EXTRA_BED = 'hotel_extra_bed';
    case RESTAURANT_SET_MENU_LUNCH = 'restaurant_set_menu_lunch';
    case RESTAURANT_SET_MENU_DINNER = 'restaurant_set_menu_dinner';
    case RESTAURANT_A_LA_CARTE = 'restaurant_a_la_carte';
    case ATTRACTION_ENTRY_TICKET = 'attraction_entry_ticket';
    case ATTRACTION_GUIDED_VISIT = 'attraction_guided_visit';

    public function label(): string
    {
        return str_replace('_', ' ', ucwords($this->value, '_'));
    }
}
