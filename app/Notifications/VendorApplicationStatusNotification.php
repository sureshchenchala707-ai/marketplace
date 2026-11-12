<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class VendorApplicationStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $status,
        public ?string $remarks = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        $approved = $this->status === 'approved';

        return [
            'title' => $approved
                ? 'Vendor Application Approved!'
                : 'Vendor Application Rejected',

            'message' => $approved
                ? 'Congratulations! Your shop has been approved. You can now access your vendor dashboard.'
                : 'Your vendor application was rejected.'
                    . ($this->remarks ? ' Remarks: ' . $this->remarks : ''),

            'url' => route('vendor.login'),
            'type' => 'vendor_application',
            'status' => $this->status,
        ];
    }
}