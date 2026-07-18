<?php

namespace App\Policies;

use App\Models\User;

class ClientPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, User $client): bool
    {
        return $user->isAdmin() && $client->admin_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, User $client): bool
    {
        return $user->isAdmin() && $client->admin_id === $user->id;
    }

    public function delete(User $user, User $client): bool
    {
        return $user->isAdmin() && $client->admin_id === $user->id;
    }
}
