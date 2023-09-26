<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Products;
use Livewire\WithFileUploads;

class ProductsLivewire extends Component
{
    use WithFileUploads;

    public function render()
    {
        $list = Products::latest()->get();
        return view('admin.products.index', compact('list'), ['page_title' => 'Products']);
    }
}
