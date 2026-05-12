<?php

namespace App\Livewire\Admin\CommodityProductEnquiry;

use App\Models\Address;
use App\Models\CommodityProduct;
use App\Models\HomeProduct;
use App\Models\PackagingType;
use App\Models\ProductEnquiry;
use App\Models\ProductEnquiryHistory;
use App\Models\SellerCommodityProductStatePrice;
use App\Models\SellerProductEnquiry;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;

class Variation extends Component
{
    public $page_title = 'Add Enquiry';

    public $hidden_id, $user_id, $variation_id = [], $variation_quantity = [], $origin_city, $purpose, $description, $price, $quality, $packaging_charge, $selected_quality, $selected_packaging_type;
    public $same_buyer_address = false;
    public $package_type_array = [];
    public $billing_address = [
        'pin_code'          => '',
        'address_line_one'  => '',
        'address_line_two'  => '',
        'city'              => '',
        'state'             => '',
    ];

    public $delivery_address = [
        'pin_code'          => '',
        'address_line_one'  => '',
        'address_line_two'  => '',
        'city'              => '',
        'state'             => '',
    ];

    public $consignee_detail = [
        'consignee_company' => '',
        'address'           => [
            'pin_code'          => '',
            'address_line_one'  => '',
            'address_line_two'  => '',
            'city'              => '',
            'state'             => '',
        ],
        'gst_number'        => '',
        'consignee_phone'   => ''
    ];

    public function mount($id)
    {
        $this->hidden_id = $id;
    }

    public function render()
    {
        $user_list = User::where('status', 'active')->orderBy('name', 'asc')->get();
        $data = HomeProduct::with('getCommodityProduct', 'getSellerCommodityProduct', 'getBrand', 'getSellerStatePrice')->findOrFail($this->hidden_id);
        $this->price = $data->base_price;
        $variations = SellerCommodityProductStatePrice::with('getSellerCommodityProduct')->where('seller_commodity_product_id', $data->seller_commodity_product_id)->get();
        
        $packagin_arr = [];
        if($data->getSellerCommodityProduct && $data->getSellerCommodityProduct->packaging_type && count($data->getSellerCommodityProduct->packaging_type) > 0){
            foreach ($data->getSellerCommodityProduct->packaging_type as $key => $type_id) {
                $packagin_type = PackagingType::find($type_id);
                if($packagin_type){
                    $packagin_data['id'] = $packagin_type->id;
                    $packagin_data['name'] = $packagin_type->name;
                    $packagin_data['charge'] = $data->getSellerCommodityProduct->packaging_type_price ? ($data->getSellerCommodityProduct->packaging_type_price && isset($data->getSellerCommodityProduct->packaging_type_price[$packagin_type->id]) ? $data->getSellerCommodityProduct->packaging_type_price[$packagin_type->id] : 0) : 0;
                    $packagin_arr[] = $packagin_data;
                }
            }
        }
        // Sort array by charge in ascending order
        usort($packagin_arr, function ($a, $b) {
            return (float)$a['charge'] <=> (float)$b['charge'];
        });
        $this->package_type_array = $packagin_arr;
        $this->initializeDefaultQualityAndPackaging($data);

        return view('admin.commodity_product_enquiry.variation', compact('data', 'variations', 'user_list'));
    }

    protected function initializeDefaultQualityAndPackaging($data)
    {
        if (is_null($this->selected_quality) && $data->getCommodityProduct && is_array($data->getCommodityProduct->quality) && count($data->getCommodityProduct->quality) > 0) {
            $firstQuality = $data->getCommodityProduct->quality[0];
            $firstPrice = $data->getCommodityProduct->quality_price[0] ?? 0;
            $this->selected_quality = $firstQuality;
            $this->quality = [
                'name' => $firstQuality,
                'price' => (string)($firstPrice ?? 0),
            ];
        }

        if (is_null($this->selected_packaging_type) && count($this->package_type_array) > 0) {
            $firstPackage = $this->package_type_array[0];
            $this->selected_packaging_type = $firstPackage['id'];
            $this->packaging_charge = $firstPackage;
        }
    }

    public function updatedSelectedQuality($value)
    {
        $data = HomeProduct::with('getCommodityProduct')->findOrFail($this->hidden_id);
        if ($data->getCommodityProduct && is_array($data->getCommodityProduct->quality)) {
            $qualityKey = array_search($value, $data->getCommodityProduct->quality, true);
            $price = $qualityKey !== false ? ($data->getCommodityProduct->quality_price[$qualityKey] ?? 0) : 0;
            $this->quality = [
                'name' => $value,
                'price' => (string)($price ?? 0),
            ];
        }
    }

    public function updatedSelectedPackagingType($value)
    {
        $selectedPackage = collect($this->package_type_array)->firstWhere('id', (int)$value);
        if ($selectedPackage) {
            $this->packaging_charge = $selectedPackage;
        }
    }

    public function getStateCityByPincode($type)
    {
        $pincode = $this->consignee_detail['address']['pin_code'];
        if($type == 'billing_address'){
            $pincode = $this->billing_address['pin_code'];
        }
        $data = Address::where('pincode', $pincode)->first();
        if($data){
            if($type == 'billing_address'){
                $this->billing_address['city']  = $data->city;
                $this->billing_address['state'] = $data->state;
            }else{
                $this->consignee_detail['address']['city'] = $data->city;
                $this->consignee_detail['address']['state'] = $data->state;
            }
        }
    }

    public function consigneeAddress()
    {
        if($this->same_buyer_address){
            $this->consignee_detail = [
                'address'           => [
                    'pin_code'          => $this->billing_address['pin_code'],
                    'address_line_one'  => $this->billing_address['address_line_one'],
                    'address_line_two'  => $this->billing_address['address_line_two'],
                    'city'              => $this->billing_address['city'],
                    'state'             => $this->billing_address['state'],
                ]
            ];

        }else{

            $this->consignee_detail = [
                'address'           => [
                    'pin_code'          => '',
                    'address_line_one'  => '',
                    'address_line_two'  => '',
                    'city'              => '',
                    'state'             => '',
                ]
            ];

        }
    }

    public function save()
    {
        $this->validate([
            'user_id'               => 'required',
            'variation_id'          => 'required|array',
            'variation_quantity'    => 'required|array',
        ]);

        $user = User::find($this->user_id);
        if(!$user){
            $this->dispatch('alert',
                type : 'error',
                message : 'User not found.',
            );
            return false;
        }

        $product = HomeProduct::with('getCommodityProduct', 'getSellerCommodityProduct')->findOrFail($this->hidden_id);
        $this->initializeDefaultQualityAndPackaging($product);
        $minimum_balance = websiteSetupValue('minimum_balance_for_enquiry') ? websiteSetupValue('minimum_balance_for_enquiry') : 0;
        $user_total_balance = $user->cash_balance + $user->credit_balance;

        if($minimum_balance > $user_total_balance){
            $this->dispatch('alert',
                type : 'error',
                message : 'Company balance is insufficient to make an enquiry. Please add funds to account.',
            );
            return false;
        }

        $variation_arr = [];
        foreach ($this->variation_id as $key => $variation_id) {
            $variation = SellerCommodityProductStatePrice::with('getSellerCommodityProduct')->find($variation_id);

            $value_arr = [];
            foreach ($variation->value as $value){
                $value['unit']  = null;
                if($variation->getSellerCommodityProduct && $variation->getSellerCommodityProduct->commodity_product_id){
                    $commodity = CommodityProduct::find($variation->getSellerCommodityProduct->commodity_product_id);
                    if($commodity && $commodity->unit){
                        $value['unit']['name']          = getProductUnit($commodity->unit[$value['name']]) ? getProductUnit($commodity->unit[$value['name']])->name : '';
                        $value['unit']['short_name']    = getProductUnit($commodity->unit[$value['name']]) ? getProductUnit($commodity->unit[$value['name']])->short_name : '';
                    }
                }
                $value_arr[] = $value;
            }

            $variation_arr[] = [
                'id'            => $variation_id,
                'value'         => $value_arr,
                'price'         => $variation->price,
                'stock'         => $variation->stock,
                'is_selected'   => $variation->is_selected,
                'user_selected' => "true",
                'quantity'      => $this->variation_quantity[$variation_id]
            ];
        }

        $data = new ProductEnquiry;
        $data->user_id              = $this->user_id;
        $data->commodity_product_id = $variation->commodity_product_id;
        $data->brand_id             = $variation->brand_id;
        $data->unique_id            = 'PE-'.date('Ymd').'-'.rand(1111, 9999);
        $data->origin_city          = $this->origin_city;
        $data->variation            = $variation_arr;
        $data->billing_address      = $this->billing_address;
        $data->consignee_detail     = $this->consignee_detail;
        $data->quality              = $this->quality;
        $data->packaging_charge     = $this->packaging_charge;
        $data->purpose              = $this->purpose;
        $data->description          = $this->description;
        $data->price                = $this->price;
        $data->history              = [['status' => 'pending', 'created_at' => Carbon::now()]];
        $data->save();

        $data_history = new ProductEnquiryHistory;
        $data_history->user_id              = $data->user_id;
        $data_history->commodity_product_id = $data->commodity_product_id;
        $data_history->brand_id             = $data->brand_id;
        $data_history->product_enquiry_id   = $data->id;
        $data_history->unique_id            = $data->unique_id;
        $data_history->origin_city          = $data->origin_city;
        $data_history->variation            = $data->variation;
        $data_history->billing_address      = $data->billing_address;
        $data_history->delivery_address     = $data->delivery_address;
        $data_history->consignee_detail     = $data->consignee_detail;
        $data_history->purpose              = $data->purpose;
        $data_history->description          = $data->description;
        $data_history->price                = $data->price;
        $data_history->save();

        $title = 'Product Enquiry';
        $body = 'Dear '.$user->name.', Your product enquiry has been successfully submitted.';
        $type = 'product_enquiry';
        $data_info = [
            'unique_id'     => $data->unique_id,
        ];
        sendNotification($user, $title, $body, $type, $data_info, true);

        // Enquiry Send to Seller
        if(websiteSetupValue('enquiry_send_to_seller') && websiteSetupValue('enquiry_send_to_seller') == 1){
            $enquiry_data = ProductEnquiry::with('getBrand', 'getCommodityProduct')->findOrFail($data->id);
            $variation_arr = [];
            foreach($enquiry_data->variation as $variations_value){
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

            foreach ($seller_ids as $user_id) {
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
                $data->quality              = $enquiry_data->quality;
                $data->packaging_charge     = $enquiry_data->packaging_charge;
                $data->message              = $enquiry_data->message;
                $data->price                = $price_arr;
                $data->base_price           = $product_state_prices[0]->getSellerCommodityProduct->base_price;
                $data->loading_address      = $product_state_prices[0]->getSellerCommodityProduct->loading_address;
                $data->status               = $data->status ?? 'pending';
                if(!$data->history){
                    $data->history          = [['status' => 'New Enquiry', 'created_at' => Carbon::now()]];
                }
                $data->save();

                $title = 'New Product Enquiry';
                $body = 'Dear '.$user->name.', Your have new product enquiry. Please fill your price.';
                $type = 'product_enquiry';
                $data_info = [
                    'unique_id'     => $data->unique_id,
                ];
                sendNotification($user, $title, $body, $type, $data_info, true);

            }

            $enquiry_data->status = count($seller_ids)!=0 ? 'Enquiry Sent To Seller' : 'No Seller Available';
            $history = $enquiry_data->history;
            $history[] = ['status' => 'Enquiry Sent To Seller', 'created_at' => Carbon::now()];
            $enquiry_data->history = $history;
            $enquiry_data->save();
        }

        session()->flash('success', 'Product enquiry created successfully !!');
        return $this->redirectRoute('admin.commodity-product-enquiry.index',navigate: true);

    }
}
