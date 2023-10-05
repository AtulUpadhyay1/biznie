<?php

namespace App\Livewire\Admin\Customer;

use Livewire\Component;

class EditInfo extends Component
{
    public function render()
    {
        return view('admin.customer_list.edit', ['page_title' => 'Edit Customer Info']);
    }
}
