<?php

namespace App\Livewire\Admin\Notification;

use Livewire\Component;
use App\Models\Notification;

class NotificationModel extends Component
{
    public $notification_count = 0;

    protected $listeners = ['newNotification' => 'fetchNotifications'];

    public function mount()
    {
        $this->fetchNotifications();
    }

    public function render()
    {
        $notifications = Notification::latest()
            ->where('title', 'New Product Enquiry')
            ->where('is_admin_read', 0)
            ->get();
        $this->notification_count = $notifications->count();
        return view('admin.notification.notification_model', compact('notifications'));
    }

    public function fetchNotifications()
    {
        $count = Notification::where('title', 'New Product Enquiry')
        ->where('is_admin_read', 0)
        ->count();
        $this->notification_count = $count;
        if($this->notification_count > 0) {
            $this->dispatch('notification-modal',
                modal : true,
            );
        }
    }

    public function markAsRead()
    {
        Notification::where('title', 'New Product Enquiry')
            ->where('is_admin_read', 0)
            ->update(['is_admin_read' => 1]);
        $this->notification_count = 0;

        $this->dispatch('notification-modal',
            modal : false,
        );
    }
}
