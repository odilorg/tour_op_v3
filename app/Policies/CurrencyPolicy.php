<?php

namespace App\Policies;

use App\Models\Currency;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CurrencyPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('currency.view');
    }

    public function view(User $user, Currency $currency): bool
    {
        return $user->can('currency.view');
    }

    public function create(User $user): bool
    {
        return $user->can('currency.manage');
    }

    public function update(User $user, Currency $currency): bool
    {
        return $user->can('currency.manage');
    }

    public function delete(User $user, Currency $currency): bool
    {
        return $user->can('currency.manage');
    }
}
