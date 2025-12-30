<?php

namespace App\Livewire\Admin\WebsiteSetup;

use App\Models\Admin;
use Livewire\Component;
use App\Models\NotificationSetting as NotificationSettingModel;

class NotificationSetting extends Component
{
    public $page_title = 'Notification Setting';
    public $notify_new_order_enquiry = true;
    public $notify_seller_reply = true;
    public $notify_booking_confirmed = true;
    public $setting;

    public function render()
    {
        return view('admin.website_setup.notification_setting');
    }

    public function mount()
    {
        $this->setting = NotificationSettingModel::where('model', Admin::class)
            ->where('model_id', 1)
            ->first();

        if ($this->setting) {
            $this->notify_new_order_enquiry = (bool) $this->setting->notify_new_order_enquiry;
            $this->notify_seller_reply = (bool) $this->setting->notify_seller_reply;
            $this->notify_booking_confirmed = (bool) $this->setting->notify_booking_confirmed;
        }
    }

    public function updateSetting()
    {
        $data = [
            'notify_new_order_enquiry' => (bool) $this->notify_new_order_enquiry,
            'notify_seller_reply' => (bool) $this->notify_seller_reply,
            'notify_booking_confirmed' => (bool) $this->notify_booking_confirmed,
        ];

        if ($this->setting) {
            $this->setting->update($data);
        } else {
            $this->setting = NotificationSettingModel::create(array_merge([
                'model' => Admin::class,
                'model_id' => 1,
            ], $data));
        }

        session()->flash('success', 'Notification settings updated successfully.');
        return $this->redirectRoute('admin.website_setup.notification_setting', navigate: true);
    }
}
