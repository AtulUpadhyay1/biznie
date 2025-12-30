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

    public $search;
    protected $queryString = [
        'search'        => ['except' => '']
    ];

    public function render()
    {
        $list = CommodityProductOrder::search($this->search)
            ->with('getCommodityProduct', 'getBrand', 'getSeller', 'getCustomer', 'getTransporter', 'getProductEnquiry')
            ->latest()
            ->paginate(getPaginate());
        $total = $list->total();
        return view('admin.commodity_product_order.index', compact('list', 'total'));
    }
}
