<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function before(User $user, string $ability, ?Project $project = null): ?bool
    {
        if (!$project) {
            return null;
        }

        if ($user->isAdmin() && $this->belongsToManagedClient($user, $project)) {
            return true;
        }

        if ($user->isClient() && $project->user_id === $user->id) {
            return true;
        }

        return null;
    }

    public function view(User $user, Project $project): bool
    {
        return false;
    }

    public function update(User $user, Project $project): bool
    {
        return false;
    }

    public function delete(User $user, Project $project): bool
    {
        return false;
    }

    public function manageTasks(User $user, Project $project): bool
    {
        return false;
    }

    public function manageFiles(User $user, Project $project): bool
    {
        return false;
    }

    public function export(User $user, Project $project): bool
    {
        return false;
    }

    private function belongsToManagedClient(User $user, ?Project $project): bool
    {
        return $project?->user?->admin_id === $user->id;
    }
}
