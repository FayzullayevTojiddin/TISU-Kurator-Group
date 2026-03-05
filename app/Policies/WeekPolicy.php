<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Week;

class WeekPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Week $week): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isDean();
    }

    public function update(User $user, Week $week): bool
    {
        return $user->isSuperAdmin() || $user->isDean();
    }

    public function delete(User $user, Week $week): bool
    {
        return $user->isSuperAdmin();
    }
}
