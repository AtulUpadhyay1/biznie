<?php

namespace App\Livewire\Admin\PackagingType;

use Livewire\Component;
use App\Models\PackagingType;
use App\Models\CommodityProduct;

class Index extends Component
{
    public $page_title = 'Packaging Type';

    public function render()
    {
        $list = PackagingType::latest()->get();
        return view('admin.packaging_type.index', compact('list'));
    }

    public function updateStatus($id)
    {
        try{
            $data = PackagingType::find($id);
            $data->status = $data->status ? 0 : 1;
            $data->save();
            $this->dispatch('alert',
                type: $data->status==1 ? 'success' : 'error',
                message: $data->status== 1 ? 'Packaging type active successfully !!': 'Packaging type inactive successfully !!'
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
            $check = CommodityProduct::whereJsonContains('packaging_type', ''.$id)->first();
            if($check){
                $this->dispatch('alert',
                    type: 'error',
                    message: "Can't delete this packaging type !!"
                );
                return true;
            }

            PackagingType::destroy($id);
            $this->dispatch('alert',
                type: 'success',
                message: 'Packaging type deleted successfully !!'
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
