<?php

namespace App\Livewire\Admin\CommodityProductEnquiry;

use Livewire\Component;
use App\Models\HomeProduct;
use Livewire\WithPagination;
use App\Models\CommodityProduct;

class Create extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $page_title = 'Select a product for enquiry';
    public function render()
    {
        $commodity = CommodityProduct::first();
        $id = 0;
        $q = HomeProduct::with('getUser', 'getCommodityProduct', 'getSellerCommodityProduct', 'getBrand');
        // if($commodity){
        //     $id = $commodity->id;
        // }
        // if($request->commodity_product_id){
        //     $id = $request->commodity_product_id;
        // }
        // if($request->search){
        //     $searchTerm = $request->search;
        //     $q->whereHas('getCommodityProduct', function ($query) use ($searchTerm) {
        //         $query->where('name', 'like', '%' . $searchTerm . '%');
        //     })->orWhereHas('getBrand', function ($query) use ($searchTerm) {
        //         $query->where('name', 'like', '%' . $searchTerm . '%');
        //     });
        // }
        $list = $q->paginate(getPaginate());
        return view('admin.commodity_product_enquiry.create', compact('list'));
    }
}
