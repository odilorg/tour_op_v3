<?php

namespace App\Policies;

use App\Models\Tour;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TourPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('tour.view');
    }

    public function view(User $user, Tour $tour): bool
    {
        return $user->can('tour.view');
    }

    public function create(User $user): bool
    {
        return $user->can('tour.create');
    }

    public function update(User $user, Tour $tour): bool
    {
        return $user->can('tour.update');
    }

    public function delete(User $user, Tour $tour): bool
    {
        return $user->can('tour.delete');
    }
}
