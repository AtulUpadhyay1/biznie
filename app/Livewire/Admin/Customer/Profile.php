<?php

namespace App\Livewire\Admin\Customer;

use App\Models\User;
use Livewire\Component;

class Profile extends Component
{
    public $data;
    public function mount($id)
    {
        $this->data = User::with(['getUserDetail'])->find($id);
    }

    public function render()
    {
        return view('admin.customer_list.profile', ['page_title' => 'Buyer Profile']);
    }
}
