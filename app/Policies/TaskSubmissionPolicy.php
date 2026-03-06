<?php

namespace App\Policies;

use App\Models\TaskSubmission;
use App\Models\User;

class TaskSubmissionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, TaskSubmission $submission): bool
    {
        return match (true) {
            $user->isSuperAdmin() => true,
            $user->isDean() => in_array($submission->group->faculty_id, $user->getDeanFacultyIds()),
            $user->isCurator() => $submission->group->curator_id === $user->id,
            default => false,
        };
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, TaskSubmission $submission): bool
    {
        return match (true) {
            $user->isSuperAdmin() => true,
            $user->isDean() => in_array($submission->group->faculty_id, $user->getDeanFacultyIds()),
            $user->isCurator() => $submission->group->curator_id === $user->id
                && $submission->status !== \App\Enums\TaskStatus::Completed,
            default => false,
        };
    }

    public function delete(User $user, TaskSubmission $submission): bool
    {
        return $user->isSuperAdmin();
    }
}
