<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;

class AllVendorsLivewire extends Component
{
    use WithFileUploads;
    public $formMode = false;

    public function render()
    {
        return view('admin.all_vendors.index', ['page_title' => 'All Vendors']);
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
