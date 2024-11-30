<?php

namespace App\Livewire\Admin\Customer;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CommodityProductOrder;

class Orders extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $data;
    public function mount($id)
    {
        $this->data = User::find($id);
    }

    public function render()
    {
        $list = CommodityProductOrder::where('customer_user_id', $this->data->id)->with('getProductEnquiry', 'getCommodityProduct')->latest()->simplePaginate(getPaginate(25));
        return view('admin.customer_list.orders', compact('list'), ['page_title' => 'Customer Orders List']);
    }
}
