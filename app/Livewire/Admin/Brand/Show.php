<?php

namespace App\Livewire\Admin\Brand;

use App\Models\Brand;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CommodityProductState;

class Show extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $hidden_id;

    public function mount($id)
    {
        $this->hidden_id = $id;
    }

    public function render()
    {
        $data = Brand::findOrFail($this->hidden_id);
        $product_list = CommodityProductState::where('brand_id', $this->hidden_id)->with(['getCommodityProduct'])->latest()->paginate(getPaginate());
        return view('admin.brand.show', compact('data', 'product_list'), ['page_title' => 'View - '. $data->name]);
    }
}
