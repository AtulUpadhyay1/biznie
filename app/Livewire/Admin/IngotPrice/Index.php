<?php

namespace App\Livewire\Admin\IngotPrice;

use Livewire\Component;
use App\Models\IngotPrice;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $list = IngotPrice::latest()->paginate(getPaginate());
        return view('admin.ingot_price.index', compact('list'), ['page_title' => 'Ingot Price']);
    }

    public function delete($id)
    {
        try {

            IngotPrice::destroy($id);
            $this->dispatch('alert',
                type: 'success',
                message: 'Brand deleted successfully !!'
            );

        } catch (\Exception $e) {

            $this->dispatch('alert',
                type: 'error',
                message: 'Something went wrong !!'
            );

        }
    }
}
