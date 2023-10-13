<?php

namespace App\Livewire\Admin\Customer;

use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;

class Orders extends Component
{
    public $data;
    public function mount($id)
    {
        $this->data = User::find($id);
    }

    public function render()
    {
        return view('admin.customer_list.orders', ['page_title' => 'Customer Orders List']);
    }
}
