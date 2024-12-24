<?php

namespace App\Livewire\Admin\Notification;

use Livewire\Component;
use App\Models\Notification;

class NavbarNotification extends Component
{
    public function render()
    {
        $notifications = Notification::latest()->with('getUser')->where('is_admin_read', 0)->get();
        return view('admin.notification.navbar_notification', compact('notifications'));
    }

    public function markAllAsRead()
    {
        try {
            $list = Notification::where('is_admin_read', 0)->latest()->get();
            foreach ($list as $item){
                $item->is_admin_read = 1;
                $item->save();
            }
            $this->dispatch('alert',
                type:'success',
                message: 'All notifications marked as read!'
            );
        } catch (\Throwable $th) {
            $this->dispatch('alert',
                type: 'error',
                message: 'Something went wrong !!'
            );
        }
    }

    public function markAsRead($notification_id)
    {
        $notification = Notification::find($notification_id);
        $notification->is_admin_read = 1;
        $notification->save();
        $this->dispatch('alert',
            type: 'success',
            message: 'Notification marked as read!'
        );
    }
}
