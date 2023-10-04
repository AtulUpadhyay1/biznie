<?php

namespace App\Livewire\Admin\Customer;

use Livewire\Component;
use Livewire\WithFileUploads;

class Orders extends Component
{
    use WithFileUploads;

    public function render()
    {
        return view('livewire.admin.customer.orders');
    }
}
