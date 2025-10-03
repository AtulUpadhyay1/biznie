<?php

namespace App\Livewire\Admin\Vehicle;

use App\Models\Vehicle;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $page_title = 'Vehicle List';

    public function mount()
    {
        $this->authorize('vehicle-list');
    }

    public function render()
    {
        $list = Vehicle::latest()->paginate(getPaginate());
        return view('admin.vehicle.index', compact('list'));
    }

    public function updateStatus($id)
    {
        try{
            $data = Vehicle::find($id);
            $data->status = $data->status ? 0 : 1;
            $data->save();
            $this->dispatch('alert',
                type: $data->status == 1 ? 'success' : 'error',
                message: $data->status == 1 ? 'Vehicle active successfully !!': 'Vehicle inactive successfully !!'
            );

        }catch (\Exception $e) {
            $this->dispatch('alert',
                type: 'error',
                message: 'Something went wrong !!'
            );
        }
    }
}
