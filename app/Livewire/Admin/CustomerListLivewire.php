<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;

class CustomerListLivewire extends Component
{
    use WithFileUploads;

    public function render()
    {
        return view('admin.customer_list.index', ['page_title' => 'Customer List']);
    }
}
