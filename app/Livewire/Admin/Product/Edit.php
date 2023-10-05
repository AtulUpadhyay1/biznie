<?php

namespace App\Livewire\Admin\Product;

use Livewire\Component;

class Edit extends Component
{
    public function render()
    {
        return view('admin.product.edit', ['page_title' => 'Edit Product']);
    }
}
