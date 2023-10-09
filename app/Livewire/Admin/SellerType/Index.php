<?php

namespace App\Livewire\Admin\SellerType;

use Livewire\Component;
use App\Models\SellerType;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;

class Index extends Component
{

    public function render()
    {
        $list = SellerType::latest()->get();
        return view('admin.seller_type.index', compact('list'), ['page_title' => 'Seller Type']);
    }

    public function updateStatus($id)
    {
        try{
            $data = SellerType::find($id);
            $data->status = $data->status ? 0 : 1;
            $data->save();
            $this->dispatch('alert',
                type: $data->status==1 ? 'success' : 'error',
                message: $data->status== 1 ? 'Seller type active successfully !!': 'Seller type inactive successfully !!'
            );
        }
        catch (\Exception $e) {
            $this->dispatch('alert',
                type: 'error',
                message: 'Something went wrong !!'
            );
        }
    }

    public function updateFeatured($id)
    {
        try{
            $data = SellerType::find($id);
            $data->featured = $data->featured ? 0 : 1;
            $data->save();

            $this->dispatch('alert',
                type: $data->featured==1 ? 'success' : 'error',
                message: $data->featured== 1 ? 'seller type featured successfully !!': 'seller type unfeatured successfully !!'
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
            SellerType::destroy($id);
            $this->dispatch('alert',
                type: 'success',
                message: 'Seller type deleted successfully !!'
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
