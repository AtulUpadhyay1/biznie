<?php

namespace App\Livewire\Admin\Customer;

use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        return view('admin.customer_list.index', ['page_title' => 'Customer List']);
    }
}
