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
        if (! $notification) {
            return;
        }
        $notification->is_admin_read = 1;
        $notification->save();
        $this->dispatch('alert',
            type: 'success',
            message: 'Notification marked as read!'
        );
    }

    /**
     * Mark read and go to whatever the notification is about — a bell entry that
     * only dismissed itself left the admin to hunt for the enquiry by hand.
     */
    public function openNotification($notification_id)
    {
        $notification = Notification::find($notification_id);
        if (! $notification) {
            return;
        }

        $notification->is_admin_read = 1;
        $notification->save();

        $payload = is_array($notification->data) ? $notification->data : [];
        $id = $payload['id'] ?? null;

        $route = match ($notification->type) {
            'general_enquiry' => $id
                ? route('admin.general-enquiry.show', $id)
                : route('admin.general-enquiry.index'),
            'contact_us' => route('admin.contact-us.index'),
            'seller_request' => $id
                ? route('admin.seller-request.show', $id)
                : route('admin.seller-request.index'),
            'product_enquiry' => $id
                ? route('admin.commodity-product-enquiry.show', $id)
                : route('admin.commodity-product-enquiry.index'),
            default => null,
        };

        if ($route) {
            return $this->redirect($route, navigate: true);
        }
    }
}
