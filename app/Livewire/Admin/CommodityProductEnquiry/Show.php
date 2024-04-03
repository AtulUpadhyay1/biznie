<?php

namespace App\Livewire\Admin\CommodityProductEnquiry;

use Livewire\Component;
use App\Models\ProductEnquiry;
use App\Models\SellerCommodityProduct;
use App\Models\SellerCommodityProductStatePrice;

class Show extends Component
{
    public $page_title = 'View Enquiry';
    public $hidden_id, $user_id = [];

    public function mount($id)
    {
        $this->hidden_id = $id;
    }

    public function render()
    {
        $data = ProductEnquiry::with('getBrand', 'getCommodityProduct')->findOrFail($this->hidden_id);
        $variation_arr = [];
        foreach($data->variation as $variations_value){
            $variation_arr[] = $variations_value['value'];
        }

        $seller_ids = SellerCommodityProductStatePrice::where('is_selected', '1')->where(function($query) use ($variation_arr){
            foreach ($variation_arr as $variation) {
                $query->orWhereJsonContains('value', $variation);
            }
        })->pluck('user_id')->unique()->toArray();

        $seller_list = SellerCommodityProduct::whereIn('user_id', $seller_ids)->with('getStatePrice', 'getBrand', 'getUser')->get();
        return view('admin.commodity_product_enquiry.show', compact('data', 'seller_list'));
    }

    public function sendEnquiry()
    {
        if(count($this->user_id) == 0){
            $this->dispatch('alert',
                type : 'error',
                message : 'There are no seller selected !!',
            );
            return false;
        }
        $enquiry_data = ProductEnquiry::findOrFail($this->hidden_id);
        $variation_arr = [];
        foreach($enquiry_data->variation as $variations_value){
            $variation_arr[] = $variations_value['value'];
        }

        foreach ($this->user_id as $user_id) {
            $state_price = SellerCommodityProductStatePrice::where('user_id', $user_id)->where('is_selected', '1')->where(function($query) use ($variation_arr){
                foreach ($variation_arr as $variation) {
                    $query->orWhereJsonContains('value', $variation);
                }
            })->first();
        }

        dd($state_price);
    }
}
