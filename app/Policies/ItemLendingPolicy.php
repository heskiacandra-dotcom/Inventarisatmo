<?php

namespace App\Policies;

use App\User;
use App\ItemLending;
use Illuminate\Auth\Access\HandlesAuthorization;

class ItemLendingPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user) { return true; }
    public function create(User $user) { return true; } // Atau user tertentu
    public function update(User $user, ItemLending $lending) { return true; }
}