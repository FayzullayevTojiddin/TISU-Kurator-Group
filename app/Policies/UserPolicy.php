<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isDean();
    }

    public function view(User $user, User $model): bool
    {
        return match (true) {
            $user->isSuperAdmin() => true,
            $user->isDean() => $model->isCurator(),
            default => $user->id === $model->id,
        };
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isDean();
    }

    public function update(User $user, User $model): bool
    {
        return match (true) {
            $user->isSuperAdmin() => true,
            $user->isDean() => $model->isCurator(),
            default => false,
        };
    }

    public function delete(User $user, User $model): bool
    {
        return $user->isSuperAdmin() && $user->id !== $model->id;
    }
}
