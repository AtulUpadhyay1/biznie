<?php

namespace App\Livewire\Admin\CommodityProductEnquiry;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\ProductEnquiry;
use App\Models\SellerProductEnquiry;
use App\Models\SellerCommodityProduct;
use App\Models\TransporterAddressPrice;
use App\Models\TransporterProductEnquiry;
use App\Models\SellerCommodityProductStatePrice;

class SellerReply extends Component
{
    public $page_title = 'View Seller Reply';
    public $hidden_id, $selected_enquiry_id, $list, $data, $set_enquiry_data, $set_enquiry_data_price = [], $set_enquiry_data_base_price, $transport_price = 0, $commission = 200;

    public $product_enquiry_data, $base_price, $selected_seller_commodity_product;

    public $transporter_list;
    public $transporter_enquiry, $transporter_price, $selected_transporter_id;

    public $active_tab = 'seller';
    protected $queryString = [
        'active_tab'        => ['except' => '']
    ];

    public function mount($id)
    {
        $this->hidden_id = $id;
        $status = ['Mark For Sell', 'replied', 'ordered'];
        $this->list = SellerProductEnquiry::where('product_enquiries_id', $this->hidden_id)
            ->with('getSellerCommodityProduct', 'getSellerCommodityProduct.getStatePrice', 'getBrand', 'getCommodityProduct')
            ->orderBy('updated_at', 'desc')
            ->get();
        foreach ($this->list as $data) {
            if($data->is_mark == 1){
                $this->selected_enquiry_id = $data->id;
            }
        }

        if($this->list->count() == 0){
            session()->flash('error', 'No sellers have responded yet.');
            return $this->redirectRoute('admin.commodity-product-enquiry.index', navigate: true);
        }
        $this->data = $this->list[0];

        $this->transporter_list = TransporterProductEnquiry::where('product_enquiries_id', $this->hidden_id)->with('getUser')->get();
        foreach ($this->transporter_list as $transporter_data) {
            if($transporter_data->is_mark == 1){
                $this->selected_transporter_id = $transporter_data->id;
            }
        }
    }

    public function render()
    {
        // $this->updatePriceForm();
        $selected_transporter = TransporterProductEnquiry::where('product_enquiries_id', $this->hidden_id)->where('is_mark', '1')->with('getUser')->first();
        // if($selected_transporter){
        //     $this->transport_price = $selected_transporter->price;
        // }
        return view('admin.commodity_product_enquiry.seller_reply', compact('selected_transporter'));
    }

    public function updatePriceForm()
    {
        if($this->selected_enquiry_id){
            $this->set_enquiry_data_price = [];
            $this->set_enquiry_data = SellerProductEnquiry::with('getSellerCommodityProduct', 'getSellerCommodityProduct.getStatePrice', 'getBrand', 'getCommodityProduct')->find($this->selected_enquiry_id);
            $seller_commodity_product = SellerCommodityProduct::where('user_id', $this->set_enquiry_data->user_id)
                ->where('commodity_product_id', $this->set_enquiry_data->commodity_product_id)
                ->where('brand_id', $this->set_enquiry_data->brand_id)
                ->first();
            $this->selected_seller_commodity_product = $seller_commodity_product;
            foreach ($this->set_enquiry_data->value as $variation) {
                $gauge_diff = SellerCommodityProductStatePrice::where('user_id', $this->set_enquiry_data->user_id)
                    ->where('commodity_product_id', $this->set_enquiry_data->commodity_product_id)
                    ->where('brand_id', $this->set_enquiry_data->brand_id)
                    // ->where('state', $state)
                    // ->where('city', $city)
                    // ->whereJsonContains('value', $variation['value'])
                    // ->where(function($query) use ($variation) {
                    //     foreach ($variation['value'] as $value) {
                    //         $query->whereJsonContains('value', $value['value']);
                    //     }
                    // })
                    ->first();
                $this->set_enquiry_data_price[] = $gauge_diff ? $gauge_diff->price : 0;
            }
            $this->set_enquiry_data_base_price = $seller_commodity_product->base_price ? $seller_commodity_product->base_price : 0;
            $this->transport_price = $this->set_enquiry_data->transport_price ? $this->set_enquiry_data->transport_price : 0;
            $this->commission = $this->set_enquiry_data->commission ? $this->set_enquiry_data->commission : $this->set_enquiry_data->getSellerCommodityProduct->commission_amount;
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
        $this->set_enquiry_data_base_price = str_replace(',', '', $this->set_enquiry_data_base_price);
        $this->commission = str_replace(',', '', $this->commission);
        $this->transport_price = str_replace(',', '', $this->transport_price);

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
        return $this->redirectRoute('admin.commodity-product-enquiry.sellerReply', $this->hidden_id, navigate: true);
    }

    public function markTransporter()
    {
        $enquiry = ProductEnquiry::findOrFail($this->hidden_id);

        if($enquiry && $enquiry->status == 'ordered'){
            $this->dispatch('alert',
                type : 'error',
                message : 'This enquiry has been converted to an order.',
            );
            return false;
        }

        TransporterProductEnquiry::where('product_enquiries_id', $this->hidden_id)->update(['is_mark' => 0]);

        $data = TransporterProductEnquiry::find($this->selected_transporter_id);
        $data->is_mark = 1;
        $data->save();

        if($enquiry->history != "Transporter Marked"){
            $enquiry->status = 'Transporter Marked';
            $history = $enquiry->history;
            $history[] = ['status' => 'Transporter Marked', 'created_at' => Carbon::now()];
            $enquiry->history = $history;
            $enquiry->save();
        }

        session()->flash('success', 'Transporter mark successfully.');
        return $this->redirectRoute('admin.commodity-product-enquiry.sellerReply', $this->hidden_id, navigate: true);
    }
}
