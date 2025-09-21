<?php

use App\Models\Booking;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

it('allows manager to update bookings', function () {
    $this->seed(DatabaseSeeder::class);
    $manager = User::where('email', 'manager@example.com')->first();
    $booking = Booking::first();

    expect($manager->can('update', $booking))->toBeTrue();
});

it('prevents viewer from updating bookings', function () {
    $this->seed(DatabaseSeeder::class);
    $viewer = User::where('email', 'viewer@example.com')->first();
    $booking = Booking::first();

    expect($viewer->can('update', $booking))->toBeFalse();
});

it('allows operator to create tours', function () {
    $this->seed(DatabaseSeeder::class);
    $operator = User::where('email', 'operator@example.com')->first();

    expect($operator->can('create', \App\Models\Tour::class))->toBeTrue();
});
