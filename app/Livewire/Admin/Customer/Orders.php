<?php

namespace App\Livewire\Admin\Customer;

use Livewire\Component;
use Livewire\WithFileUploads;

class Orders extends Component
{
    use WithFileUploads;

    public function render()
    {
        return view('admin.customer_list.orders', ['page_title' => 'Customer Orders List']);
    }
}
