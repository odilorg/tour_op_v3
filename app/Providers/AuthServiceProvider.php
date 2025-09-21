<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Currency;
use App\Models\Supplier;
use App\Models\Tour;
use App\Policies\BookingPolicy;
use App\Policies\CurrencyPolicy;
use App\Policies\SupplierPolicy;
use App\Policies\TourPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Booking::class => BookingPolicy::class,
        Supplier::class => SupplierPolicy::class,
        Tour::class => TourPolicy::class,
        Currency::class => CurrencyPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
