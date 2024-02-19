<?php

namespace App\Livewire\Seller\CommodityProduct;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\SellerCommodityProduct;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $page_title = "Product List";

    public function render()
    {
        $list = SellerCommodityProduct::where('user_id', auth()->user()->getBusiness->user_id)->paginate(getPaginate());
        return view('seller.commodity_product.index', compact('list'))->layout('seller.layouts.app');
    }
}
