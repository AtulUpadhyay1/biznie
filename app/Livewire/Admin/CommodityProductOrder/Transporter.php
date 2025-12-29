<?php

namespace App\Livewire\Admin\CommodityProductOrder;

use Carbon\Carbon;
use App\Models\User;
use Livewire\Component;
use App\Models\ProductEnquiry;
use App\Models\TransporterDetail;
use App\Models\CommodityProductOrder;
use App\Models\TransporterAddressPrice;
use App\Models\TransporterProductEnquiry;

class Transporter extends Component
{
    public $page_title = 'Order Transporter';
    public $hidden_id, $data, $transporter_user_id = [];
    public $transporter_enquiry, $transporter_price, $selected_transporter_id;

    public function mount($id)
    {
        $this->hidden_id = $id;
        $this->data = CommodityProductOrder::with('getBrand', 'getCommodityProduct', 'getDrivers', 'getSeller', 'getCustomer', 'getTransporter', 'getProductEnquiry')->findOrFail($this->hidden_id);
        $this->page_title = 'Order Transporter '. $this->data->getProductEnquiry->unique_id;

        $selected_transporters = TransporterProductEnquiry::where('product_enquiries_id', $this->data->product_enquiries_id)
            ->pluck('user_id')
            ->toArray();
        $this->transporter_user_id = $selected_transporters;

    }

    public function render()
    {
        $transporters_ids = TransporterDetail::whereJsonContains('commodity_product', $this->data->commodity_product_id)
            ->pluck('user_id');

        $transporter_list = TransporterAddressPrice::whereIn('user_id', $transporters_ids)
            ->where('state', $this->data->billing_address['state'])
            ->where('city', $this->data->billing_address['city'])
            ->with('getUser')
            ->get();

        $user_ids = $transporter_list->pluck('user_id');
        $existing_enquiries = TransporterProductEnquiry::whereIn('user_id', $user_ids)
            ->where('product_enquiries_id', $this->data->product_enquiries_id)
            ->get()
            ->keyBy('user_id');

        $transporter_list->each(function($transporter) use ($existing_enquiries) {
            $transporter->enquiry_data = $existing_enquiries->get($transporter->user_id);
        });

        return view('admin.commodity_product_order.transporter', compact('transporter_list'));
    }

    public function sendTransporterEnquiry()
    {
        try {

            if(count($this->transporter_user_id) == 0){
                $this->dispatch('alert',
                    type : 'error',
                    message : 'There are no transporter selected.',
                );
                return false;
            }

            $enquiry_data = ProductEnquiry::findOrFail($this->data->product_enquiries_id);

            // if($enquiry_data && $enquiry_data->status == 'ordered'){
            //     $this->dispatch('alert',
            //         type : 'error',
            //         message : 'This enquiry has been converted to an order.',
            //     );
            //     return false;
            // }
            foreach ($this->transporter_user_id as $transporter_user_id) {
                $data = TransporterProductEnquiry::where('user_id', $transporter_user_id)->where('product_enquiries_id', $enquiry_data->id)->first();
                $available_transport = TransporterAddressPrice::where('user_id', $transporter_user_id)
                    ->where('state', $enquiry_data->billing_address['state'])
                    ->where('city', $enquiry_data->billing_address['city'])
                    ->first();

                if(!$data){
                    $data                   = new TransporterProductEnquiry;
                }
                $data->user_id              = $transporter_user_id;
                $data->product_enquiries_id = $enquiry_data->id;
                $data->customer_user_id     = $enquiry_data->user_id;
                $data->commodity_product_id = $enquiry_data->commodity_product_id;
                $data->brand_id             = $enquiry_data->brand_id;
                $data->unique_id            = $enquiry_data->unique_id;
                $data->origin_city          = $enquiry_data->origin_city;
                $data->billing_address      = $enquiry_data->billing_address;
                $data->delivery_address     = $enquiry_data->delivery_address;
                $data->consignee_detail     = $enquiry_data->consignee_detail;
                $data->purpose              = $enquiry_data->purpose;
                $data->description          = $enquiry_data->description;
                $data->message              = $enquiry_data->message;
                $data->min_price            = $available_transport->min_price;
                $data->max_price            = $available_transport->max_price;
                $data->status               = $data->status ?? 'pending';
                if(!$data->history){
                    $data->history          = [['status' => 'New Enquiry', 'created_at' => Carbon::now()]];
                }
                $data->save();

                $user = User::find($transporter_user_id);

                $title = 'Received New Product Enquiry';
                $body = 'Dear '.$user->name.', Your have new product enquiry. Please fill your price.';
                $type = 'product_enquiry';
                $data_info = [
                    'unique_id'     => $data->unique_id,
                ];
                sendNotification($user, $title, $body, $type, $data_info, true);

            }

            $enquiry_data->status = count($this->transporter_user_id)!=0 ? 'Enquiry Sent To Transporters' : 'No Transporters Available';
            $history = $enquiry_data->history;
            $history[] = ['status' => 'Enquiry Sent To Transporters', 'created_at' => Carbon::now()];
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

    public function setTransporterPrice($id)
    {
        $this->transporter_enquiry = TransporterProductEnquiry::find($id);
        $this->transporter_price = $this->transporter_enquiry->price ?? 0;
    }

    public function updateTransporterPrice()
    {
        $this->validate([
            'transporter_price'    => 'required|min:1'
        ]);
        $data = $this->transporter_enquiry;
        $data->price = $this->transporter_price;
        $data->status = 'replied';
        $history = $data->history;
        $history[] = ['status' => 'Replied', 'created_at' => Carbon::now()];
        $data->history = $history;
        $data->save();

        $enquiry = ProductEnquiry::findOrFail($data->product_enquiries_id);
        if($enquiry){
            if($enquiry->status != 'Transporter Replied'){
                $enquiry->status = 'Transporter Replied';
                $history = $enquiry->history;
                $history[] = ['status' => 'Transporter Replied', 'created_at' => Carbon::now()];
                $enquiry->history = $history;
                $enquiry->save();
            }
        }

        session()->flash('success', 'Transporter price updated successfully.');
        return $this->redirectRoute('admin.commodity-product-order.transporter', $this->hidden_id, navigate: true);
    }

    public function markTransporter($enquiry_id)
    {
        $enquiry = ProductEnquiry::findOrFail($this->hidden_id);

        // if($enquiry && $enquiry->status == 'ordered'){
        //     $this->dispatch('alert',
        //         type : 'error',
        //         message : 'This enquiry has been converted to an order.',
        //     );
        //     return false;
        // }

        TransporterProductEnquiry::where('product_enquiries_id', $this->hidden_id)->update(['is_mark' => 0]);

        $data = TransporterProductEnquiry::find($enquiry_id);
        $data->is_mark = 1;
        $data->save();

        $order = CommodityProductOrder::where('product_enquiries_id', $data->product_enquiries_id)->first();

        if($order){
            $order->transporter_user_id = $data->user_id;
            $order->save();
        }

        if($enquiry->history != "Transporter Marked"){
            $enquiry->status = 'Transporter Marked';
            $history = $enquiry->history;
            $history[] = ['status' => 'Transporter Marked', 'created_at' => Carbon::now()];
            $enquiry->history = $history;
            $enquiry->save();
        }

        session()->flash('success', 'Transporter mark successfully.');
        return $this->redirectRoute('admin.commodity-product-order.transporter', $this->hidden_id, navigate: true);
    }
}
