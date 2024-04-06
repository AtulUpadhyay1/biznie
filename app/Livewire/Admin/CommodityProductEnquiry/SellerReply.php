<?php

namespace App\Livewire\Admin\CommodityProductEnquiry;

use Livewire\Component;
use App\Models\SellerProductEnquiry;

class SellerReply extends Component
{
    public $page_title = 'View Seller Reply';
    public $hidden_id, $selected_enquiry_id, $list, $data;

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
        return view('admin.commodity_product_enquiry.seller_reply');
    }

    public function markSeller()
    {
        try {
            SellerProductEnquiry::where('commodity_product_id', $this->hidden_id)->update(['is_mark' => 0]);
            $enquiry_data = SellerProductEnquiry::with('getUser', 'getCustomer')->find($this->selected_enquiry_id);
            $enquiry_data->update(['is_mark' => 1]);

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

            $this->dispatch('alert',
                type : 'success',
                message : 'Enquiry mark to seller successfully.',
            );
        } catch (\Throwable $th) {
            $this->dispatch('alert',
                type : 'error',
                message : 'Something went wrong.',
            );
        }

    }
}
