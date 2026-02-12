<?php

namespace App\Policies;

use App\User;
use App\RoomBooking;
use Illuminate\Auth\Access\HandlesAuthorization;

class RoomBookingPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user) { return true; }
    public function create(User $user) { return true; }
}