<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;

class DashboardLivewire extends Component
{
    public function render()
    {
        $total_customer = User::where('type', 'customer')->count();
        $total_seller = User::where('type', 'seller')->count();
        $total_transporter = User::where('type', 'transporter')->count();
        return view('admin.dashboard', compact('total_customer', 'total_seller', 'total_transporter'), ['page_title' => 'Admin Dashboard']);
    }
}
