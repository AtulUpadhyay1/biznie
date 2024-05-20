<?php

namespace App\Livewire\Admin\CommodityProductEnquiry;

use Carbon\Carbon;
use App\Models\User;
use Livewire\Component;
use App\Models\ProductEnquiry;
use App\Models\SellerProductEnquiry;
use App\Models\SellerCommodityProduct;
use App\Models\SellerCommodityProductStatePrice;

class Show extends Component
{
    public $page_title = 'View Enquiry';
    public $hidden_id, $user_id = [];

    public function mount($id)
    {
        $this->hidden_id = $id;
        $selected_user_id = SellerProductEnquiry::where('product_enquiries_id', $id)->pluck('user_id')->toArray();
        $this->user_id = $selected_user_id;
    }

    public function render()
    {
        $data = ProductEnquiry::with('getBrand', 'getCommodityProduct')->findOrFail($this->hidden_id);
        $variation_arr = [];
        foreach($data->variation as $variations_value){
            foreach($variations_value['value'] as $variation){
                $variation_data['id']      = $variation['id'];
                $variation_data['name']    = $variation['name'];
                $variation_data['value']   = $variation['value'];
                $variation_arr[] = $variation_data;
            }
        }

        $seller_ids = SellerCommodityProductStatePrice::where(function($query) use ($variation_arr){
            foreach ($variation_arr as $variation) {
                $query->orWhereJsonContains('value', $variation)->where('is_selected', '1');
            }
        })->pluck('user_id')->toArray();
        $seller_ids_with_count = array_count_values($seller_ids);
        $seller_ids = array_keys(array_filter($seller_ids_with_count, function($count) use ($variation_arr){
            return $count === count($variation_arr);
        }));

        $seller_list = SellerCommodityProduct::whereIn('user_id', $seller_ids)->with('getStatePrice', 'getBrand', 'getUser')->get();
        return view('admin.commodity_product_enquiry.show', compact('data', 'seller_list', 'variation_arr'));
    }

    public function sendEnquiry()
    {
        try {

            if(count($this->user_id) == 0){
                $this->dispatch('alert',
                    type : 'error',
                    message : 'There are no seller selected.',
                );
                return false;
            }

            $enquiry_data = ProductEnquiry::findOrFail($this->hidden_id);

            if($enquiry_data && $enquiry_data->status == 'ordered'){
                $this->dispatch('alert',
                    type : 'error',
                    message : 'This enquiry has been converted to an order.',
                );
                return false;
            }

            $variation_arr = [];
            foreach($enquiry_data->variation as $variations_value){
                foreach($variations_value['value'] as $variation){
                    $variation_data['id']      = $variation['id'];
                    $variation_data['name']    = $variation['name'];
                    $variation_data['value']   = $variation['value'];
                    $variation_arr[] = $variation_data;
                }
            }

            foreach ($this->user_id as $user_id) {
                $product_state_prices = SellerCommodityProductStatePrice::where('user_id', $user_id)->where(function($query) use ($variation_arr){
                    foreach ($variation_arr as $variation) {
                        $query->orWhereJsonContains('value', $variation);
                    }
                })->with('getSellerCommodityProduct')->get();

                $price_arr = [];
                foreach ($product_state_prices as $key => $product_state_price) {
                    $price_arr[] = $product_state_price->price;
                }

                $new_variation_arr = [];
                foreach($enquiry_data->variation as $key => $enquiry_variations){
                    $enquiry_variations = $enquiry_variations;
                    $enquiry_variations['price'] = $price_arr[$key] ?? 0;
                    if($enquiry_variations['price'] == 0){
                        $enquiry_variations['is_selected'] = "0";
                    }
                    $new_variation_arr[] = $enquiry_variations;
                }

                $data = SellerProductEnquiry::where('user_id', $user_id)->where('product_enquiries_id', $enquiry_data->id)->first();
                if(!$data){
                    $data                   = new SellerProductEnquiry;
                }
                $data->user_id              = $user_id;
                $data->product_enquiries_id = $enquiry_data->id;
                $data->customer_user_id     = $enquiry_data->user_id;
                $data->commodity_product_id = $enquiry_data->commodity_product_id;
                $data->brand_id             = $enquiry_data->brand_id;
                $data->unique_id            = $enquiry_data->unique_id;
                $data->origin_city          = $enquiry_data->origin_city;
                $data->value                = $new_variation_arr;
                $data->billing_address      = $enquiry_data->billing_address;
                $data->delivery_address     = $enquiry_data->delivery_address;
                $data->consignee_detail     = $enquiry_data->consignee_detail;
                $data->purpose              = $enquiry_data->purpose;
                $data->description          = $enquiry_data->description;
                $data->message              = $enquiry_data->message;
                $data->price                = $price_arr;
                $data->base_price           = $product_state_prices[0]->getSellerCommodityProduct->base_price;
                $data->loading_address      = $product_state_prices[0]->getSellerCommodityProduct->loading_address;
                $data->status               = $data->status ?? 'pending';
                if(!$data->history){
                    $data->history          = [['status' => 'New Enquiry', 'created_at' => Carbon::now()]];
                }
                $data->save();

                $user = User::find($user_id);

                $title = 'New Product Enquiry';
                $body = 'Dear '.$user->name.', Your have new product enquiry. Please fill your price.';
                $type = 'product_enquiry';
                $data_info = [
                    'unique_id'     => $data->unique_id,
                ];
                sendNotification($user, $title, $body, $type, $data_info, true);

            }

            $enquiry_data->status = 'Enquiry Send To Seller';
            $history = $enquiry_data->history;
            $history[] = ['status' => 'Enquiry Send To Seller', 'created_at' => Carbon::now()];
            $enquiry_data->history = $history;
            $enquiry_data->save();

            $this->dispatch('alert',
                type : 'success',
                message : 'Enquiry sent successfully.',
            );

        } catch (\Throwable $th) {

            $this->dispatch('alert',
                type : 'error',
                message : 'Something went wrong.',
            );

        }

    }
}
