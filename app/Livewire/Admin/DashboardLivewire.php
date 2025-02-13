<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Brand;
use Livewire\Component;
use App\Models\ProductEnquiry;
use App\Models\CommodityProductOrder;

class DashboardLivewire extends Component
{
    public function render()
    {
        $total_customer = User::where('type', 'customer')->count();
        $total_seller = User::where('type', 'seller')->count();
        $total_transporter = User::where('type', 'transporter')->count();
        $total_brand = Brand::count();
        $total_enquiry = ProductEnquiry::count();
        $total_order = CommodityProductOrder::count();

        return view('admin.dashboard', compact(
            'total_customer',
            'total_seller',
            'total_transporter',
            'total_brand',
            'total_enquiry',
            'total_order'
        ), ['page_title' => 'Admin Dashboard']);
    }

    public function notificationTest()
    {
        $user = User::find(39);
        sendNotification($user, 'Hi Test', 'This is test notification.', $type="notification", [], false);

        $this->dispatch('alert',
            type: 'success',
            message: 'Notification sent successfully.'
        );
    }
}
