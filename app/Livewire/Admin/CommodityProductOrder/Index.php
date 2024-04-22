<?php

namespace App\Livewire\Admin\CommodityProductOrder;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CommodityProductOrder;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $page_title = 'Commodity Product Orders';

    public function render()
    {
        $total = CommodityProductOrder::count();
        $list = CommodityProductOrder::with('getCommodityProduct', 'getBrand', 'getSeller', 'getCustomer')->latest()->paginate(getPaginate());
        return view('admin.commodity_product_order.index', compact('list', 'total'));
    }
}
