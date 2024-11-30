<?php

namespace App\Livewire\Admin\Seller;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CommodityProductOrder;

class Orders extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $data, $status;
    public function mount($id)
    {
        $this->data = User::where('type', 'seller')->with('getSellerKycDetail', 'getBusiness')->findOrFail($id);
        $this->status = $this->data->getSellerKycDetail->status;
    }

    public function render()
    {
        $list = CommodityProductOrder::where('seller_user_id', $this->data->id)->with('getProductEnquiry', 'getCommodityProduct')->latest()->simplePaginate(getPaginate(25));
        return view('admin.seller.orders', compact('list'), ['page_title' => 'Seller Order List']);
    }
}
