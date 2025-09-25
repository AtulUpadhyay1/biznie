<?php

namespace App\Livewire\Admin\SellerProduct;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\SellerCommodityProduct;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $page_title = '';
    public $user_id, $search;
    public function mount($user_id)
    {
        $this->user_id = $user_id;
        $user = User::with('getBusiness')->find($this->user_id);
        $this->page_title = $user->name .' ('.$user->phone.') - '.$user->getBusiness->name;
    }

    public function render()
    {
        $list = SellerCommodityProduct::where('user_id', $this->user_id)->with('getCategory', 'getSubCategory', 'getUnit', 'getBrand', 'getStatePrice')->latest()->get();
        return view('admin.seller_product.index', compact('list'));
    }

    public function updateStatus($id)
    {
        try {

            $data = SellerCommodityProduct::findOrFail($id);
            $data->status = $data->status == 'active' ? 'inactive' : 'active';
            $data->save();

            $this->dispatch('alert',
                type : 'success',
                message : 'Product status updated successfully.',
            );

        } catch (\Throwable $th) {
            $this->dispatch('alert',
                type : 'error',
                message : 'something went wrong',
            );
        }

    }
}
