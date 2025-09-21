<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookingPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('booking.view');
    }

    public function view(User $user, Booking $booking): bool
    {
        return $user->can('booking.view');
    }

    public function create(User $user): bool
    {
        return $user->can('booking.create');
    }

    public function update(User $user, Booking $booking): bool
    {
        return $user->can('booking.update');
    }

    public function delete(User $user, Booking $booking): bool
    {
        return $user->can('booking.delete');
    }
}
