<?php

namespace App\Livewire\Admin\SellerProduct;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\SellerCommodityProduct;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $user_id, $search;
    public function mount($user_id)
    {
        $this->user_id = $user_id;
    }

    public function render()
    {
        $list = SellerCommodityProduct::where('user_id', $this->user_id)->with('getCategory', 'getSubCategory', 'getUnit', 'getBrand', 'getStatePrice')->latest()->get();
        return view('admin.seller_product.index', compact('list'), ['page_title' => 'Seller Product']);
    }
}
