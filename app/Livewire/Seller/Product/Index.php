<?php

namespace App\Livewire\Seller\Product;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $page_title = "Product List";
    public function render()
    {
        $list = Product::where('user_id', auth()->user()->getBusiness->user_id)->paginate(getPaginate());
        return view('seller.product.index', compact('list'))->layout('seller.layouts.app');
    }
}
