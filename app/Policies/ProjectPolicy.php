<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function view(User $user, Project $project): bool
    {
        return $user->isAdmin() && $this->belongsToManagedClient($user, $project);
    }

    public function update(User $user, Project $project): bool
    {
        return $user->isAdmin() && $this->belongsToManagedClient($user, $project);
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->isAdmin() && $this->belongsToManagedClient($user, $project);
    }

    public function manageTasks(User $user, Project $project): bool
    {
        return $user->isAdmin() && $this->belongsToManagedClient($user, $project);
    }

    public function manageFiles(User $user, Project $project): bool
    {
        return $user->isAdmin() && $this->belongsToManagedClient($user, $project);
    }

    public function export(User $user, Project $project): bool
    {
        return $user->isAdmin() && $this->belongsToManagedClient($user, $project);
    }

    private function belongsToManagedClient(User $user, Project $project): bool
    {
        return $project->user?->admin_id === $user->id;
    }
}
