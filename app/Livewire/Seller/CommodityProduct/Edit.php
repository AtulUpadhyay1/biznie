<?php

namespace App\Livewire\Seller\CommodityProduct;

use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use App\Models\SellerCommodityProduct;

class Edit extends Component
{
    public $page_title = "Edit Commodity Product";
    use WithFileUploads;

    public $hidden_id, $name;

    public function mount($id)
    {
        $this->hidden_id    = $id;
        $data               = SellerCommodityProduct::find($this->hidden_id);
        $this->name         = $data->name;
    }

    public function render()
    {
        return view('seller.commodity_product.edit')->layout('seller.layouts.app');
    }

    public function update()
    {
        $this->validate([
            'name'  => 'required',
        ]);
        try {

            $data = SellerCommodityProduct::find($this->hidden_id);
            if(!$data){
                $this->dispatch('alert',
                    type : 'error',
                    message : 'Product not found.',
                );
                return true;
            }
            $data->name = $this->name;
            $data->slug = Str::slug($this->name);
            $data->save();

            session()->flash('success', 'Product updated successfully !!');
            return $this->redirect('/seller/commodity-product',navigate: true);

        } catch (\Throwable $th) {
            $this->dispatch('alert',
                type : 'error',
                message : 'Something went wrong !!',
            );
            return true;
        }

    }
}
