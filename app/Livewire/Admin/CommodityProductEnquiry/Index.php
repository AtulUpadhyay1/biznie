<?php

namespace App\Livewire\Admin\CommodityProductEnquiry;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ProductEnquiry;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $page_title = 'Commodity Product Enquiry';

    public function render()
    {
        $total = ProductEnquiry::count();
        $list = ProductEnquiry::latest()->with('getBrand')->paginate(getPaginate());
        return view('admin.commodity_product_enquiry.index', compact('total', 'list'));
    }
}
