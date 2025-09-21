<?php

use App\Enums\BookingAssignmentRole;
use App\Enums\BookingAssignmentStatus;
use App\Enums\BookingStatus;
use App\Jobs\RecomputeBookingFinancialsJob;
use App\Models\Booking;
use App\Models\BookingAssignment;
use App\Models\BookingDay;
use App\Models\Currency;
use App\Models\Supplier;

it('calculates booking progress based on assignment statuses', function () {
    Currency::create(['code' => 'USD', 'name' => 'US Dollar']);
    $booking = Booking::create([
        'reference_code' => 'TEST-01',
        'customer_name' => 'Test',
        'start_date' => now(),
        'end_date' => now(),
        'party_size' => 2,
        'status' => BookingStatus::REQUESTED->value,
        'currency_code' => 'USD',
    ]);

    $day = BookingDay::create([
        'booking_id' => $booking->id,
        'date' => now(),
        'day_index' => 1,
        'title' => 'Day 1',
    ]);

    $supplier = Supplier::create([
        'type' => \App\Enums\SupplierType::GUIDE,
        'name' => 'Guide One',
    ]);

    BookingAssignment::create([
        'booking_id' => $booking->id,
        'booking_day_id' => $day->id,
        'supplier_id' => $supplier->id,
        'role' => BookingAssignmentRole::GUIDE->value,
        'qty' => 1,
        'rate_minor' => 1000,
        'currency_code' => 'USD',
        'status' => BookingAssignmentStatus::PENDING->value,
    ]);

    BookingAssignment::create([
        'booking_id' => $booking->id,
        'booking_day_id' => $day->id,
        'supplier_id' => $supplier->id,
        'role' => BookingAssignmentRole::GUIDE->value,
        'qty' => 1,
        'rate_minor' => 1000,
        'currency_code' => 'USD',
        'status' => BookingAssignmentStatus::SUPPLIER_CONFIRMED->value,
    ]);

    $booking->refresh();
    $booking->recalculateProgress();

    expect($booking->progress_percent)->toBe(50);
});

it('recomputes totals via job', function () {
    Currency::firstOrCreate(['code' => 'USD'], ['name' => 'US Dollar']);
    $booking = Booking::create([
        'reference_code' => 'TEST-02',
        'customer_name' => 'Test',
        'start_date' => now(),
        'end_date' => now(),
        'party_size' => 2,
        'status' => BookingStatus::REQUESTED->value,
        'currency_code' => 'USD',
    ]);

    $day = BookingDay::create([
        'booking_id' => $booking->id,
        'date' => now(),
        'day_index' => 1,
        'title' => 'Day 1',
    ]);

    $supplier = Supplier::create([
        'type' => \App\Enums\SupplierType::GUIDE,
        'name' => 'Guide Two',
    ]);

    BookingAssignment::create([
        'booking_id' => $booking->id,
        'booking_day_id' => $day->id,
        'supplier_id' => $supplier->id,
        'role' => BookingAssignmentRole::GUIDE->value,
        'qty' => 2,
        'rate_minor' => 2000,
        'cost_minor' => 1500,
        'currency_code' => 'USD',
        'status' => BookingAssignmentStatus::SUPPLIER_CONFIRMED->value,
    ]);

    (new RecomputeBookingFinancialsJob($booking))->handle();

    $booking->refresh();
    expect($booking->list_total_minor)->toBe(4000)
        ->and($booking->cost_total_minor)->toBe(3000)
        ->and($booking->profit_minor)->toBe(1000);
});

it('updates booking status to completed when assignments fulfilled', function () {
    Currency::firstOrCreate(['code' => 'USD'], ['name' => 'US Dollar']);
    $booking = Booking::create([
        'reference_code' => 'TEST-03',
        'customer_name' => 'Test',
        'start_date' => now()->subDay(),
        'end_date' => now()->subDay(),
        'party_size' => 2,
        'status' => BookingStatus::CONFIRMED->value,
        'currency_code' => 'USD',
    ]);

    $day = BookingDay::create([
        'booking_id' => $booking->id,
        'date' => now()->subDay(),
        'day_index' => 1,
        'title' => 'Day 1',
    ]);

    $supplier = Supplier::create([
        'type' => \App\Enums\SupplierType::GUIDE,
        'name' => 'Guide Three',
    ]);

    $assignment = BookingAssignment::create([
        'booking_id' => $booking->id,
        'booking_day_id' => $day->id,
        'supplier_id' => $supplier->id,
        'role' => BookingAssignmentRole::GUIDE->value,
        'qty' => 1,
        'rate_minor' => 1000,
        'currency_code' => 'USD',
        'status' => BookingAssignmentStatus::FULFILLED->value,
    ]);

    $booking->refresh();
    $booking->recalculateProgress();

    expect($booking->status)->toBe(BookingStatus::COMPLETED);
});
