<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;

class CustomerProfileLivewire extends Component
{
    use WithFileUploads;
    public $formMode = false;

    public function render()
    {
        return view('admin.all_customer.profile', ['page_title' => 'Customer Profile']);
    }

    public function edit()
    {
        $this->formMode =  true;
    }

    public function cancel()
    {
        $this->formMode = false;
    }
}
