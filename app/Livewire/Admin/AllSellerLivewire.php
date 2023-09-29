<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;

class AllSellerLivewire extends Component
{
    use WithFileUploads;
    public $formMode = false;

    public function render()
    {
        return view('admin.all_seller.index', ['page_title' => 'All Seller']);
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
