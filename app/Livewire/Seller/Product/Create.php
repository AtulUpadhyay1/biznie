<?php

namespace App\Livewire\Seller\Product;

use Livewire\Component;

class Create extends Component
{
    public $page_title = "Product add";

    public function render()
    {
        
        return view('seller.product.create')->layout('seller.layouts.app');
    }
}
