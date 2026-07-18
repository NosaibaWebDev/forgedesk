<?php

namespace App\Policies;

use App\Models\ProjectFile;
use App\Models\User;

class ProjectFilePolicy
{
    public function view(User $user, ProjectFile $file): bool
    {
        return $file->uploaded_by === $user->id;
    }

    public function delete(User $user, ProjectFile $file): bool
    {
        return $file->uploaded_by === $user->id || $user->isAdmin();
    }

    public function download(User $user, ProjectFile $file): bool
    {
        return true;
    }
}
