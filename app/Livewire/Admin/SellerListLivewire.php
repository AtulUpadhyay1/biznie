<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;

class SellerListLivewire extends Component
{
    use WithFileUploads;
    public $formMode = false;

    public function render()
    {
        return view('admin.seller_list.index', ['page_title' => 'All Seller']);
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
