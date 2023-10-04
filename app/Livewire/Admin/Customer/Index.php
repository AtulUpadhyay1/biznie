<?php

namespace App\Livewire\Admin\Customer;

use Livewire\Component;
use Livewire\WithFileUploads;

class Index extends Component
{
    use WithFileUploads;

    public function render()
    {
        return view('admin.customer_list.index', ['page_title' => ' Customer List']);
    }
}
