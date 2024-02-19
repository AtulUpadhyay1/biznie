<?php

namespace App\Livewire\Seller;

use Livewire\Component;

class DashboardLivewire extends Component
{
    public $page_title = 'Seller Dashboard';
    public function render()
    {
        return view('seller.dashboard')->layout('seller.layouts.app');
    }
}
