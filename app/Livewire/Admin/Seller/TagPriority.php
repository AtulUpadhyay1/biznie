<?php

namespace App\Livewire\Admin\Seller;

use App\Models\UserDetail;
use Livewire\Component;
use App\Models\SellerTag;

class TagPriority extends Component
{
    public $data, $tag = [], $priority;
    public function mount($id)
    {
        $this->data = UserDetail::where('user_id', $id)->first();
        $this->tag  = $this->data->tag ?? [];
        $this->priority = $this->data->priority;
    }

    public function render()
    {
        $seller_tag = SellerTag::where('status', 1)->latest()->get();
        return view('admin.seller.tag_priority', compact('seller_tag'), ['page_title' => 'Tag & Priority']);
    }

    public function save()
    {
        // try {
            $this->data->tag        = $this->tag ?? [];
            $this->data->priority   = $this->priority;
            $this->data->save();

            $this->dispatch('alert',
                type : 'success',
                message : 'Tag & Priority Updated Successfully.',
            );

        // } catch (\Throwable $th) {
        //     $this->dispatch('alert',
        //         type : 'error',
        //         message : 'Something Went Wrong. Please try again later.',
        //     );
        // }

    }
}
