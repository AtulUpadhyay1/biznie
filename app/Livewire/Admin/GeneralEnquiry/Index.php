<?php

namespace App\Livewire\Admin\GeneralEnquiry;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\GeneralEnquiry;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $page_title = 'Commodity Product Enquiry';

    public function render()
    {
        $list = GeneralEnquiry::with('getBrand', 'getSellerCommodityProduct', 'getUser')->latest()->paginate(getPaginate());
        return view('admin.general_enquiry.index', compact('list'));
    }
}
