<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TestMarketplaceNotification extends Notification
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Welcome to ESS Marketplace!',
            'message' => 'Your notification system is working successfully.',
            'url' => route('notifications.index'),
            'type' => 'system',
        ];
    }
}