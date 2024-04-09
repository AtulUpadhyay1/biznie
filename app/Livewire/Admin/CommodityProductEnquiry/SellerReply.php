<?php

namespace App\Livewire\Admin\CommodityProductEnquiry;

use Livewire\Component;
use App\Models\SellerProductEnquiry;

class SellerReply extends Component
{
    public $page_title = 'View Seller Reply';
    public $hidden_id, $selected_enquiry_id, $list, $data, $set_enquiry_data, $set_enquiry_data_price = [], $set_enquiry_data_base_price, $transport_price = 0, $commission = 200;

    public function mount($id)
    {
        $this->hidden_id = $id;

        $this->list = SellerProductEnquiry::where('commodity_product_id', $this->hidden_id)->where('status', 'replied')->with('getSellerCommodityProduct', 'getSellerCommodityProduct.getStatePrice', 'getBrand', 'getCommodityProduct')->get();
        foreach ($this->list as $data) {
            $this->selected_enquiry_id = $data->is_mark == 1 ? $data->id : '';
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

    public function markSeller()
    {
        $this->validate([
            'set_enquiry_data_base_price'   => 'required|min:0',
            'transport_price'               => 'required|min:0',
            'commission'                    => 'required|min:0',
            'set_enquiry_data_price'        => 'required',
        ]);
        try {
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
            return $this->redirectRoute('admin.commodity-product.show', $this->hidden_id, navigate: true);

        } catch (\Throwable $th) {
            $this->dispatch('alert',
                type : 'error',
                message : 'Something went wrong.',
            );
        }

    }
}
