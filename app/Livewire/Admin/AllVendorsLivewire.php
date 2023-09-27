<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class AllVendorsLivewire extends Component
{
    public function render()
    {
        return view('admin.all_vendors.index', ['page_title' => 'All Vendors']);
    }
}
