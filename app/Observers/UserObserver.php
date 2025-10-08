<?php

// app/Observers/UserObserver.php
namespace App\Observers;

use App\Models\User;
use App\Models\UserDocument;

class UserObserver
{
    public function saved(User $user): void
    {
        $verifiedChangedToTrue = $user->wasChanged('verified') && (int)$user->verified === 1;
        $userVerifiedChangedToTrue = $user->wasChanged('user_verified') && (int)$user->user_verified === 1;

        if ($verifiedChangedToTrue || $userVerifiedChangedToTrue) {
            \App\Models\UserDocument::where('user_id', $user->id)
                ->where('status', '!=', 'aprobado')
                ->update(['status' => 'aprobado']);
        }
    }
}

