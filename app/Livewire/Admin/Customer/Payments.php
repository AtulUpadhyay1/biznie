<?php

namespace App\Livewire\Admin\Customer;

use App\Models\User;
use Livewire\Component;

class Payments extends Component
{
    public $data;
    public function mount($id)
    {
        $this->data = User::find($id);
    }

    public function render()
    {
        return view('admin.customer_list.payment', ['page_title' => 'Customer Payment List']);
    }
}
