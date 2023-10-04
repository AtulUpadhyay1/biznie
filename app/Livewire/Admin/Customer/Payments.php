<?php

namespace App\Livewire\Admin\Customer;

use Livewire\Component;
use Livewire\WithFileUploads;

class Payments extends Component
{
    use WithFileUploads;

    public function render()
    {
        return view('admin.customer_list.payment', ['page_title' => 'Customer Payment List']);
    }
}
