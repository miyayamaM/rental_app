<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Rental;
use Illuminate\Auth\Access\HandlesAuthorization;

class RentalPolicy
{
    use HandlesAuthorization;

    public function destroy(User $user, Rental $rental) {
        return $user->id === $rental->user_id;
    }
}
