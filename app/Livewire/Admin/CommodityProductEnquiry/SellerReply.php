<?php

namespace App\Livewire\Admin\CommodityProductEnquiry;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\ProductEnquiry;
use App\Models\SellerProductEnquiry;

class SellerReply extends Component
{
    public $page_title = 'View Seller Reply';
    public $hidden_id, $selected_enquiry_id, $list, $data, $set_enquiry_data, $set_enquiry_data_price = [], $set_enquiry_data_base_price, $transport_price = 0, $commission = 200;

    public $product_enquiry_data, $base_price;

    public function mount($id)
    {
        $this->hidden_id = $id;
        $status = ['Mark For Sell', 'replied', 'ordered'];
        $this->list = SellerProductEnquiry::where('product_enquiries_id', $this->hidden_id)->with('getSellerCommodityProduct', 'getSellerCommodityProduct.getStatePrice', 'getBrand', 'getCommodityProduct')->get();
        foreach ($this->list as $data) {
            $this->selected_enquiry_id = $data->is_mark == 1 ? $data->id : '';
        }
        if($this->list->count() == 0){
            session()->flash('error', 'No sellers have responded yet.');
            return $this->redirectRoute('admin.commodity-product-enquiry.index', navigate: true);
        }
        $this->data = $this->list[0];
    }

    public function render()
    {
        $this->updatePriceForm();
        return view('admin.commodity_product_enquiry.seller_reply');
    }

    public function updatePriceForm()
    {
        if($this->selected_enquiry_id){
            $this->set_enquiry_data_price = [];
            $this->set_enquiry_data = SellerProductEnquiry::with('getSellerCommodityProduct', 'getSellerCommodityProduct.getStatePrice', 'getBrand', 'getCommodityProduct')->find($this->selected_enquiry_id);
            foreach ($this->set_enquiry_data->value as $variation) {
                $this->set_enquiry_data_price[] = $variation['price'];
            }
            $this->set_enquiry_data_base_price = $this->set_enquiry_data->base_price ?? 0;
            $this->transport_price = $this->set_enquiry_data->transport_price ?? 0;
            $this->commission = $this->set_enquiry_data->commission ?? 0;
        }
    }

    public function setBasePrice($id)
    {
        $this->product_enquiry_data = SellerProductEnquiry::with('getSellerCommodityProduct', 'getSellerCommodityProduct.getStatePrice', 'getBrand', 'getCommodityProduct')->find($id);
        $this->base_price = $this->product_enquiry_data->base_price ?? 0;
    }

    public function updateBasePrice()
    {
        $this->validate([
            'base_price'    => 'required|min:1'
        ]);
        $data = $this->product_enquiry_data;
        $data->base_price = $this->base_price;
        $data->save();

        session()->flash('success', 'Base price updated successfully.');
        return $this->redirectRoute('admin.commodity-product-enquiry.sellerReply', $this->hidden_id, navigate: true);
    }

    public function markSeller()
    {
        $this->validate([
            'set_enquiry_data_base_price'   => 'required|min:0',
            'transport_price'               => 'required|min:0',
            'commission'                    => 'required|min:0',
            'set_enquiry_data_price'        => 'required',
        ]);
        try {

            $enquiry = ProductEnquiry::findOrFail($this->hidden_id);

            if($enquiry && $enquiry->status == 'ordered'){
                $this->dispatch('alert',
                    type : 'error',
                    message : 'This enquiry has been converted to an order.',
                );
                return false;
            }

            if($enquiry->history != "Seller Marked"){
                $enquiry->status = 'Seller Marked';
                $history = $enquiry->history;
                $history[] = ['status' => 'Seller Marked', 'created_at' => Carbon::now()];
                $enquiry->history = $history;
                $enquiry->save();
            }

            SellerProductEnquiry::where('commodity_product_id', $this->hidden_id)->update(['is_mark' => 0]);

            $enquiry_data = SellerProductEnquiry::with('getUser', 'getCustomer')->find($this->selected_enquiry_id);

            $new_variation_arr = [];
            foreach ($enquiry_data->value as $key => $variation) {
                $variation['price'] = $this->set_enquiry_data_price[$key] ?? 0;
                $new_variation_arr[] = $variation;

            }
            $enquiry_data->value = $new_variation_arr;
            $enquiry_data->base_price = $this->set_enquiry_data_base_price;
            $enquiry_data->transport_price = $this->transport_price;
            $enquiry_data->commission = $this->commission;
            $enquiry_data->price = $this->set_enquiry_data_price;
            $enquiry_data->is_mark = 1;
            if($enquiry_data->history != "Mark For Sell"){
                $enquiry_data->status = 'Mark For Sell';
                $history = $enquiry_data->history;
                $history[] = ['status' => 'Mark For Sell', 'created_at' => Carbon::now()];
                $enquiry_data->history = $history;
            }
            $enquiry_data->save();

            // Seller Notification
            // $seller = $enquiry_data->getUser;
            // $title = 'Enquiry ' .$enquiry_data->unique_id. ' Marked';
            // $body = 'Dear '.$seller->name.', Your product enquiry mark for sell.';
            // $type = 'product_enquiry';
            // $data_info = [
            //     'unique_id'     => $enquiry_data->unique_id,
            // ];
            // sendNotification($seller, $title, $body, $type, $data_info, true);

            // Customer Notification
            $customer = $enquiry_data->getCustomer;
            $title = 'Enquiry ' .$enquiry_data->unique_id. ' Marked Seller';
            $body = 'Dear '.$customer->name.', Seller marked for enquiry.';
            $type = 'product_enquiry';
            $data_info = [
                'unique_id'     => $enquiry_data->unique_id,
            ];
            sendNotification($customer, $title, $body, $type, $data_info, true);

            session()->flash('success', 'Enquiry mark to seller successfully.');
            return $this->redirectRoute('admin.commodity-product-enquiry.sellerReply', $this->hidden_id, navigate: true);

        } catch (\Throwable $th) {
            $this->dispatch('alert',
                type : 'error',
                message : 'Something went wrong.',
            );
        }

    }
}
