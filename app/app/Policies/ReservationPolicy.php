<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReservationPolicy
{
    use HandlesAuthorization;

    /**
     * 自分の登録した予約のみキャンセルできるポリシー
     *
     * @param User $user
     * @param Reservation $reservation
     * @return bool
     */
    public function destroy(User $user, Reservation $reservation): bool
    {
        return $user->id === $reservation->user_id;
    }
}
