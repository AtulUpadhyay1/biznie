<?php

namespace App\Livewire\Admin\Product;

use Livewire\Component;
use Livewire\WithFileUploads;

class Index extends Component
{
    use WithFileUploads;

    public function render()
    {
        return view('admin.product.index', ['page_title' => 'Product List']);
    }
}
