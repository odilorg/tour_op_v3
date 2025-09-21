<?php

namespace App\Enums;

enum BookingAssignmentStatus: string
{
    case PENDING = 'pending';
    case REQUESTED = 'requested';
    case SUPPLIER_CONFIRMED = 'supplier_confirmed';
    case COMPANY_CONFIRMED = 'company_confirmed';
    case FULFILLED = 'fulfilled';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::REQUESTED => 'Requested',
            self::SUPPLIER_CONFIRMED => 'Supplier Confirmed',
            self::COMPANY_CONFIRMED => 'Company Confirmed',
            self::FULFILLED => 'Fulfilled',
            self::CANCELLED => 'Cancelled',
        };
    }
}
