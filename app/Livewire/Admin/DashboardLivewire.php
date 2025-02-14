<?php

namespace App\Livewire\Admin;

use Carbon\Carbon;
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

        $last_7_days_customer = [];
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $count = User::where('type', 'seller')->whereDate('created_at', $date)->count();
            $last_7_days_customer[] = ['date' => $date, 'count' => $count];
        }

        $last_7_days_seller = [];
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $count = User::where('type', 'seller')->whereDate('created_at', $date)->count();
            $last_7_days_seller[] = ['date' => $date, 'count' => $count];
        }

        return view('admin.dashboard', compact(
            'total_customer',
            'total_seller',
            'total_transporter',
            'total_brand',
            'total_enquiry',
            'total_order',
            'last_7_days_customer',
            'last_7_days_seller'
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
