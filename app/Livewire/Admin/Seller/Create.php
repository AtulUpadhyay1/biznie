<?php

namespace App\Livewire\Admin\Seller;

use Livewire\Component;

class Create extends Component
{
    public function render()
    {
        return view('admin.seller.form', ['page_title' => 'Seller Create']);
    }
}
