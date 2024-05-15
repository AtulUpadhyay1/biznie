<?php

namespace App\Livewire\Admin\IdentityType;

use Livewire\Component;
use App\Models\IdentityType;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    public $search;
    public function render()
    {
        $list = IdentityType::search($this->search)->latest()->paginate(getPaginate());
        return view('admin.identity_type.index', compact('list'), ['page_title' => 'Identity Type']);
    }

    public function updateStatus($id)
    {
        try{
            $data = IdentityType::find($id);
            $data->status = $data->status ? 0 : 1;
            $data->save();
            $this->dispatch('alert',
                type: $data->status==1 ? 'success' : 'error',
                message: $data->status== 1 ? 'Identity type active successfully !!': 'Identity type inactive successfully !!'
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
        try
        {
            IdentityType::destroy($id);
            $this->dispatch('alert',
                type: 'success',
                message: 'Identity type deleted successfully !!'
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
