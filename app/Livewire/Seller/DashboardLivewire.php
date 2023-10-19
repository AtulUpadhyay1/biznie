<?php

namespace App\Livewire\Seller;

use Livewire\Component;

class DashboardLivewire extends Component
{
    public function render()
    {
        return view('seller.dashboard', ['page_title' => 'Seller Dashboard'])->layout('seller.layouts.app');
    }
}
