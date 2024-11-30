<?php

namespace App\Livewire\Admin\CommodityProduct;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CommodityProduct;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $page_title = 'Commodity Product';

    public function render()
    {
        $list = CommodityProduct::latest()->with('getCategory')->paginate(getPaginate());
        $total = $list->total();
        return view('admin.commodity_product.index', compact('total', 'list'));
    }

    public function updateStatus($id)
    {
        try {

            $data = CommodityProduct::findOrFail($id);
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
