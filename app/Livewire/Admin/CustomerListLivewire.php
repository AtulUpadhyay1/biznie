<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;

class CustomerListLivewire extends Component
{
    use WithFileUploads;
    public $formMode = false;

    public function render()
    {
        return view('admin.all_customer.index', ['page_title' => 'All Customers']);
    }

    public function create()
    {
        $this->formMode = true;
    }

    public function cancel()
    {
        $this->formMode = false;
    }
}
