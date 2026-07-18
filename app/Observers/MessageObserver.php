<?php

namespace App\Observers;

use App\Models\Message;
use Illuminate\Support\Facades\Cache;

class MessageObserver
{
    public function created(Message $message): void
    {
        Cache::forget("user_{$message->receiver_id}_unread_messages");
        Cache::forget("user_{$message->sender_id}_unread_messages");
    }

    public function updated(Message $message): void
    {
        if ($message->isDirty('is_read')) {
            Cache::forget("user_{$message->receiver_id}_unread_messages");
        }
    }

    public function deleted(Message $message): void
    {
        Cache::forget("user_{$message->receiver_id}_unread_messages");
        Cache::forget("user_{$message->sender_id}_unread_messages");
    }
}
