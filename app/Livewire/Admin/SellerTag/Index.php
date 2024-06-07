<?php

namespace App\Livewire\Admin\SellerTag;

use Livewire\Component;
use App\Models\SellerTag;

class Index extends Component
{
    public function render()
    {
        $list = SellerTag::latest()->get();
        return view('admin.seller_tag.index', compact('list'), ['page_title' => 'Seller Tag']);
    }

    public function updateStatus($id)
    {
        try{
            $data = SellerTag::find($id);
            $data->status = $data->status ? 0 : 1;
            $data->save();
            $this->dispatch('alert',
                type: $data->status==1 ? 'success' : 'error',
                message: $data->status== 1 ? 'Seller tag active successfully !!': 'Seller tag inactive successfully !!'
            );
        } catch (\Throwable $th) {
            $this->dispatch('alert',
                type : 'error',
                message : 'something went wrong',
            );
        }
    }

    public function delete($id)
    {
        try{
            SellerTag::destroy($id);
            $this->dispatch('alert',
                type: 'success',
                message: 'Seller type deleted successfully !!'
            );
        } catch (\Throwable $th) {
            $this->dispatch('alert',
                type : 'error',
                message : 'something went wrong',
            );
        }
    }
}
