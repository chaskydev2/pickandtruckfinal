<?php

namespace App\Policies;

use App\Models\Bid;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

// App/Policies/BidPolicy.php
class BidPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Bid $bid): bool
    {
        return $user->id === $bid->user_id
            || ($bid->bideable && $user->id === $bid->bideable->user_id);
    }

    public function update(User $user, Bid $bid): bool
    {
        return !$bid->isLocked() && $user->id === $bid->user_id;
    }

    public function delete(User $user, Bid $bid): bool
    {
        return !$bid->isLocked() && $user->id === $bid->user_id;
    }

    public function confirmCompletion(User $user, Bid $bid): bool
    {
        if ($bid->estado !== Bid::ST_PEND_CONF) return false;

        return $user->id === $bid->user_id
            || ($bid->bideable && $user->id === $bid->bideable->user_id);
    }

    public function requestCompletion(User $user, Bid $bid): bool
    {
        if ($bid->estado !== Bid::ST_ACEPTADO) return false;

        return $user->id === $bid->user_id
            || ($bid->bideable && $user->id === $bid->bideable->user_id);
    }
}

