<?php

namespace App\Livewire\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Brand;
use Livewire\Component;
use App\Mail\EnquiryMail;
use App\Models\ProductEnquiry;
use App\Models\CommodityProduct;
use Illuminate\Support\Facades\Mail;
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
        $total_commodity_product = CommodityProduct::where('status', 'active')->count();

        $today = today();
        $today_enquiry = ProductEnquiry::whereDate('created_at', $today)->count();
        $today_order = CommodityProductOrder::whereDate('created_at', $today)->count();
        $today_order_amount = CommodityProductOrder::whereDate('created_at', $today)->sum('total_amount');

        $last_7_days_customer = [];
        $last_7_days_seller = [];

        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');

            $customer_count = User::where('type', 'customer')->whereDate('created_at', $date)->count();
            $seller_count = User::where('type', 'seller')->whereDate('created_at', $date)->count();

            $last_7_days_customer[] = ['date' => $date, 'count' => $customer_count];
            $last_7_days_seller[] = ['date' => $date, 'count' => $seller_count];
        }

        return view('admin.dashboard', compact(
            'total_customer',
            'total_seller',
            'total_transporter',
            'total_brand',
            'total_enquiry',
            'total_order',
            'last_7_days_customer',
            'last_7_days_seller',
            'today_enquiry',
            'today_order',
            'today_order_amount',
            'total_commodity_product'
        ), ['page_title' => 'Admin Dashboard']);
    }

    public function notificationTest()
    {
        Mail::to('techuptechnologies1@gmail.com')->send(new EnquiryMail());
        $this->dispatch('alert',
            type: 'success',
            message: 'Notification sent successfully.'
        );


    }
}
