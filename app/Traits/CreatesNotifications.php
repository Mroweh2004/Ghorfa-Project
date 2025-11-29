<?php

namespace App\Traits;

use App\Models\Notification;
use App\Models\User;

trait CreatesNotifications
{
    protected function createNotification(User $user, string $type, string $title, string $message, $notifiable = null)
    {
        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'notifiable_type' => $notifiable ? get_class($notifiable) : null,
            'notifiable_id' => $notifiable ? $notifiable->id : null,
        ]);
    }
}

