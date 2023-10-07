<?php

namespace App\Livewire\Admin\Seller;

use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;

    public function render()
    {
        return view('admin.seller_list.form', ['page_title' => 'Edit Seller']);
    }
}
