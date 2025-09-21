<?php

use App\Enums\BookingAssignmentRole;
use App\Enums\BookingAssignmentStatus;
use App\Enums\BookingStatus;
use App\Enums\SupplierRateServiceType;
use App\Enums\SupplierRateUnit;
use App\Enums\SupplierType;
use App\Models\Booking;
use App\Models\Currency;
use App\Models\Supplier;
use App\Models\SupplierRate;
use App\Models\Tour;
use App\Models\TourDay;
use Illuminate\Support\Carbon;

it('creates booking days from a tour template and assigns suppliers', function () {
    Currency::create(['code' => 'USD', 'name' => 'US Dollar']);

    $tour = Tour::create([
        'title' => 'Sample Tour',
        'slug' => 'sample-tour',
        'default_currency_code' => 'USD',
    ]);

    foreach ([
        ['day_index' => 1, 'title' => 'Day 1'],
        ['day_index' => 2, 'title' => 'Day 2'],
        ['day_index' => 3, 'title' => 'Day 3'],
    ] as $dayData) {
        TourDay::create($dayData + ['tour_id' => $tour->id]);
    }

    $booking = Booking::create([
        'tour_id' => $tour->id,
        'reference_code' => 'FLOW-1',
        'customer_name' => 'Flow Test',
        'start_date' => Carbon::parse('2024-01-01'),
        'end_date' => Carbon::parse('2024-01-03'),
        'party_size' => 2,
        'status' => BookingStatus::REQUESTED->value,
        'currency_code' => 'USD',
    ]);

    $booking->load('tour.days');
    $booking->generateDaysFromTour();

    expect($booking->days()->count())->toBe(3)
        ->and($booking->days()->first()->date->isSameDay(Carbon::parse('2024-01-01')))->toBeTrue();

    $supplier = Supplier::create([
        'type' => SupplierType::GUIDE,
        'name' => 'Guide Flow',
    ]);

    $rate = SupplierRate::create([
        'supplier_id' => $supplier->id,
        'service_type' => SupplierRateServiceType::GUIDE_FULL_DAY,
        'unit' => SupplierRateUnit::PER_DAY,
        'amount_minor' => 1200,
        'currency_code' => 'USD',
    ]);

    $assignment = $booking->assignments()->create([
        'booking_day_id' => $booking->days()->first()->id,
        'supplier_id' => $supplier->id,
        'role' => BookingAssignmentRole::GUIDE->value,
        'service_type' => $rate->service_type->value,
        'unit' => $rate->unit->value,
        'qty' => 1,
        'rate_minor' => $rate->amount_minor,
        'currency_code' => 'USD',
        'status' => BookingAssignmentStatus::PENDING->value,
    ]);

    expect($booking->assignments()->count())->toBe(1)
        ->and($assignment->booking_id)->toBe($booking->id);
});
