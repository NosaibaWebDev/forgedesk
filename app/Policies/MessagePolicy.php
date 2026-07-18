<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;

class MessagePolicy
{
    public function view(User $user, Message $message): bool
    {
        return $message->sender_id === $user->id || $message->receiver_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }
}
