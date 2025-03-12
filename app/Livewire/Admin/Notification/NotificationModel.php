<?php

namespace App\Livewire\Admin\Notification;

use Livewire\Component;
use App\Models\Notification;

class NotificationModel extends Component
{
    public $notification_count = 0;
    public function render()
    {
        $notifications = Notification::latest()
            ->where('title', 'New Product Enquiry')
            ->where('is_admin_read', 0)
            ->get();
        $this->notification_count = $notifications->count();
        return view('admin.notification.notification_model', compact('notifications'));
    }

    public function updateCount()
    {
        $this->notification_count = Notification::where('title', 'New Product Enquiry')
            ->where('is_admin_read', 0)
            ->count();

    }

    public function markAsRead()
    {
        $notifications = Notification::where('title', 'New Product Enquiry')
            ->where('is_admin_read', 0)
            ->get();
        foreach ($notifications as $notification) {
            $notification->is_admin_read = 1;
            $notification->save();
        }
        $this->updateCount();
    }
}
