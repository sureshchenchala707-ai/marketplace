<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $orderNumber,
        public string $recipientType = 'customer'
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        $title = match ($this->recipientType) {
            'vendor' => 'New Order Received!',
            'admin' => 'New Marketplace Order!',
            default => 'Order Placed Successfully!',
        };

        $message = match ($this->recipientType) {
            'vendor' => "You have received order {$this->orderNumber}. Please review it in your vendor dashboard.",
            'admin' => "A new marketplace order has been placed: {$this->orderNumber}.",
            default => "Your order {$this->orderNumber} has been placed successfully.",
        };

        $url = match ($this->recipientType) {
            'vendor' => route('vendor.orders.index'),
            'admin' => route('admin.orders.index'),
            default => route('account.orders.index'),
        };

        return [
            'title' => $title,
            'message' => $message,
            'order_number' => $this->orderNumber,
            'url' => $url,
            'type' => 'new_order',
            'recipient_type' => $this->recipientType,
        ];
    }


}
