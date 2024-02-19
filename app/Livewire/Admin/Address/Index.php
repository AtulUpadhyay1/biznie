<?php

namespace App\Livewire\Admin\Address;

use App\Models\Address;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $page_title = 'Address List';

    public function render()
    {
        $list = Address::latest()->paginate(getPaginate());
        return view('admin.address.index', compact('list'));
    }

    public function updateStatus($id)
    {
        try{
            $data = Address::find($id);
            $data->status = $data->status ? 0 : 1;
            $data->save();
            $this->dispatch('alert',
                type: $data->status==1 ? 'success' : 'error',
                message: $data->status== 1 ? 'Address active successfully !!': 'Address inactive successfully !!'
            );
        }
        catch (\Exception $e) {
            $this->dispatch('alert',
                type: 'error',
                message: 'Something went wrong !!'
            );
        }
    }

    public function delete($id)
    {
        try{
            Address::destroy($id);
            $this->dispatch('alert',
                type: 'success',
                message: 'Address deleted successfully !!'
            );
        }
        catch (\Exception $e) {
            $this->dispatch('alert',
                type: 'error',
                message: 'Something went wrong !!'
            );
        }
    }
}
