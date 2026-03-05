<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\User;

class GroupPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Group $group): bool
    {
        return match (true) {
            $user->isSuperAdmin() => true,
            $user->isDean() => $group->faculty_id === $user->faculty_id,
            $user->isCurator() => $group->curator_id === $user->id,
            default => false,
        };
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isDean();
    }

    public function update(User $user, Group $group): bool
    {
        return match (true) {
            $user->isSuperAdmin() => true,
            $user->isDean() => $group->faculty_id === $user->faculty_id,
            default => false,
        };
    }

    public function delete(User $user, Group $group): bool
    {
        return $user->isSuperAdmin();
    }
}
