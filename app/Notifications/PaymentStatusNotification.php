<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $status,
        public string $orderNumber,
        public ?string $reason = null,
        public string $recipientType = 'customer'
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        $paid = $this->status === 'paid';

        $title = match ([$this->recipientType, $paid]) {
            ['customer', true] => 'Payment Successful!',
            ['vendor', true] => 'Customer Payment Received!',
            ['admin', true] => 'Order Payment Received!',
            ['customer', false] => 'Payment Failed',
            default => 'Payment Failed for an Order',
        };

        $message = $paid
            ? "Payment for order {$this->orderNumber} was successfully captured."
            : "Payment for order {$this->orderNumber} failed."
                . ($this->reason ? " Reason: {$this->reason}" : '');

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
            'type' => $paid ? 'payment_success' : 'payment_failed',
            'payment_status' => $this->status,
        ];
    }
}
