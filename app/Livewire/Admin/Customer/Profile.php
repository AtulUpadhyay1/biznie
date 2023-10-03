<?php

namespace App\Livewire\Admin\Customer;

use Livewire\Component;

class Profile extends Component
{
    public function render()
    {
        return view('admin.customer_list.profile', ['page_title' => 'Customer Profile']);
    }
}
