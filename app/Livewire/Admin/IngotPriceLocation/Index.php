<?php

namespace App\Livewire\Admin\IngotPriceLocation;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\IngotPriceLocation;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $list = IngotPriceLocation::latest()->paginate(getPaginate());
        return view('admin.ingot_price_location.index', compact('list'), ['page_title' => 'Ingot Price Location']);
    }

    public function updateStatus($id)
    {
        $data = IngotPriceLocation::where('id', $id)->first();
        $data->status = $data->status ? 0 : 1;
        $data->save();
        $this->dispatch('alert',
            type : 'success',
            message : 'Ingot Price Location Status Updated Successfully.',
        );
    }

    public function updateDefault($id)
    {
        $data = IngotPriceLocation::where('id', $id)->first();
        IngotPriceLocation::where('id', '!=', $id)->update(['is_default' => 0]);
        $data->is_default = $data->is_default ? 0 : 1;
        $data->save();
        $this->dispatch('alert',
            type : 'success',
            message : 'Ingot Price Location Default Updated Successfully.',
        );
    }
}
