<?php

namespace App\Livewire\Admin\ProductWiseSellerBuyer;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Brand;
use Livewire\Component;
use App\Models\PackagingType;
use App\Models\ProductEnquiry;
use App\Models\CommodityProduct;
use App\Models\TransporterDetail;
use App\Models\CommodityProductOrder;
use App\Models\CommodityProductState;
use App\Models\SellerCommodityProduct;
use App\Models\TransporterAddressPrice;
use App\Models\CommodityProductVariation;
use App\Models\CommodityProductStatePrice;
use App\Models\SellerCommodityProductHistory;
use App\Models\SellerCommodityProductStatePrice;

class Index extends Component
{
    public $page_title = 'Product Wise Seller & Buyer';

    public $product_id, $brand_id, $city, $order_by, $price_validity, $quality;
    public $brand_list, $seller_list, $customer_list, $quality_list = [];
    public $detail = null;

    protected $queryString = [
        'product_id'    => ['except' => ''],
        'brand_id'      => ['except' => ''],
        'city'          => ['except' => ''],
        'order_by'      => ['except' => ''],
        'price_validity' => ['except' => ''],
        'quality'       => ['except' => ''],
    ];

    public function mount()
    {
        $this->authorize('product_wise_seller_buyer-list');
        if ($this->product_id || $this->brand_id || $this->city || $this->order_by || $this->price_validity || $this->quality) {
            $this->search();
        }
    }

    public function render()
    {
        $product_list = CommodityProduct::latest()->with('getCategory')->get();
        $seller_commodity_product_state = SellerCommodityProductStatePrice::groupBy('city')->get();
        return view('admin.product_wise_seller_buyer.index', compact('product_list', 'seller_commodity_product_state'));
    }

    public function search()
    {
        $commodity_product = CommodityProduct::where('id', $this->product_id)->first();
        $this->quality_list = $commodity_product ? $commodity_product->quality : [];

        $seller_list = SellerCommodityProduct::where('commodity_product_id', $this->product_id);
        $brand_ids = $seller_list->pluck('brand_id')->unique()->toArray();
        if($this->brand_id){
            $seller_list = $seller_list->where('brand_id', $this->brand_id);
        }

        if($this->city){
            $state_price_data = SellerCommodityProductStatePrice::where('id', $this->city)->first();
            $seller_commodity_product_ids = SellerCommodityProductStatePrice::where('commodity_product_id', $this->product_id)
                ->where('state', $state_price_data->state)
                ->where('city', $state_price_data->city)
                ->pluck('seller_commodity_product_id')->unique()
                ->toArray();

            $seller_list = $seller_list->whereIn('id', $seller_commodity_product_ids);
        }

        if($this->order_by == 'price_validity_asc'){
            $seller_list = $seller_list->orderBy('price_validity', 'ASC');
        } elseif($this->order_by == 'price_validity_desc'){
            $seller_list = $seller_list->orderBy('price_validity', 'DESC');
        } else {
            $seller_list = $seller_list->orderBy('id', 'DESC');
        }

        if($this->price_validity == 'valid'){
            $seller_list = $seller_list->where('price_validity', '>=', now());
        } elseif($this->price_validity == 'expired'){
            $seller_list = $seller_list->where('price_validity', '<', now());
        }

        if($this->quality){
            $seller_list = $seller_list->whereJsonContains('quality', $this->quality);
        }


        $this->seller_list = $seller_list->with('getStatePrice', 'getBrand', 'getUser', 'getCommodityProduct', 'getUser.getBusiness', 'getUser.getUserDetail')->get();

        $customer_ids = ProductEnquiry::where('commodity_product_id', $this->product_id);
        if($this->brand_id){
            $customer_ids = $customer_ids->where('brand_id', $this->brand_id);
        }
        $customer_ids = $customer_ids->pluck('user_id')->unique()->toArray();
        $this->customer_list = User::whereIn('id', $customer_ids)->with('getUserDetail')->get();

        foreach ($this->customer_list as $customer_data) {

            $total_enquiry = ProductEnquiry::where('user_id', $customer_data->id)
            ->where('commodity_product_id', $this->product_id);
            if($this->brand_id){
                $total_enquiry = $total_enquiry->where('brand_id', $this->brand_id);
            }
            $total_enquiry = $total_enquiry->count();

            $customer_data->total_enquiry = $total_enquiry;

            $total_order = ProductEnquiry::where('user_id', $customer_data->id)
                ->where('commodity_product_id', $this->product_id);
            if($this->brand_id){
                $total_order = $total_order->where('brand_id', $this->brand_id);
            }
            $total_order = $total_order->where('status', 'ordered')->count();
            $customer_data->total_order = $total_order;

            $total_dispatched_order = CommodityProductOrder::where('customer_user_id', $customer_data->id)
                ->where('commodity_product_id', $this->product_id);
            if($this->brand_id){
                $total_dispatched_order = $total_dispatched_order->where('brand_id', $this->brand_id);
            }
            $total_dispatched_order = $total_dispatched_order->where('status', 'dispatched')->count();
            $customer_data->total_dispatched_order = $total_dispatched_order;

        }
        $this->brand_list = Brand::whereIn('id', $brand_ids)->get();
    }

    public function viewPriceCalculation($id)
    {
        $data = SellerCommodityProduct::with('getCommodityProduct', 'getBrand', 'getStatePrice')->findOrFail($id);

        $detail_data = [
            'id'                            => $data->id,
            'commodity_product_id'          => $data->commodity_product_id,
            'seller_commodity_product_id'   => $data->id,
            'name'                          => $data->name,
            'description'                   => $data->description,
            'brand'                         => ['id' => $data->getBrand->id, 'name' => $data->getBrand->name],
            'unit'                          => ['id' => $data->getCommodityProduct->getUnit->id, 'name' => $data->getCommodityProduct->getUnit->name],
            'address'                       => isset($data->getStatePrice[0]) ? $data->getStatePrice[0]->city : null,
            'base_price'                    => $data->base_price,
            'thumbnail'                     => $data->thumbnail ? imageUrl($data->thumbnail) : asset('common/images/no-photo.png'),
            'images'                        => [],
            'loading_charge'                => $data->loading_charge,
            'loading_position'              => $data->loading_position,
            'price_validity'                => $data->price_validity ? dateTimeFormat($data->price_validity) : null,
            'insurance_charge'              => $data->insurance_charge,
            'quality_charge'                => $data->quality_charge,
            'gst'                           => $data->gst,
            'tcs'                           => $data->tcs,
            'min_order_qty'                 => 0,
            'order_amount_type'             => $data->order_amount_type,
            'required_order_amount'         => $data->required_order_amount,
            'charts'                        => [],
            'other_charges'                 => [],
            'ex_price'                      => 0,
            'freight_price'                 => 0,
            'default_variation'             => [],
            'packaging_charge'              => [],
        ];
        if($data->getCommodityProduct){
            $detail_data['min_order_qty'] = $data->getCommodityProduct->min_order_qty;
            $detail_data['last_updated'] = dateTimeFormat($data->updated_at);

            $detail_data['quality'] = $data->getCommodityProduct->quality;
            $detail_data['quality_price'] = $data->getCommodityProduct->quality_price;
        }
        $transporters_ids = TransporterDetail::whereJsonContains('commodity_product', $data->commodity_product_id)
            ->pluck('user_id');

        $userDetail = auth()->user()->getUserDetail;
        if ($userDetail && $userDetail->state && $userDetail->city) {
            $transporter_address_price = TransporterAddressPrice::whereIn('user_id', $transporters_ids)
            ->where('state', $userDetail->state)
            ->where('city', $userDetail->city)
            ->orderBy('min_price', 'asc')
            ->first();

            if ($transporter_address_price) {
                $detail_data['freight_price'] = (int) $transporter_address_price->min_price;
            }
        }
        $product_state = CommodityProductState::where('commodity_product_id', $data->commodity_product_id)->where('brand_id', $data->brand_id)->first();
        if ($product_state && $product_state->chart) {
            foreach ($product_state->chart ?? [] as $chart) {
                $detail_data['charts'][] = imageUrl($chart);
            }
        }

        $default_variation_price = 0;
        $default_variation = CommodityProductVariation::where('commodity_product_id', $data->commodity_product_id)
            ->where('is_default', 1)
            ->first();

        if($default_variation){

            foreach($default_variation->value as $value){
                $value['unit']  = null;

                if($data->getCommodityProduct){
                    $commodity = $data->getCommodityProduct;
                    if($commodity && $commodity->unit){
                        $value['unit']['name'] = getProductUnit($commodity->unit[$value['name']]) ? getProductUnit($commodity->unit[$value['name']])->name : '';
                        $value['unit']['short_name'] = getProductUnit($commodity->unit[$value['name']]) ? getProductUnit($commodity->unit[$value['name']])->short_name : '';
                    }
                }

                $detail_data['default_variation'][] = $value;
            }

            if($default_variation){
                $state_price = CommodityProductStatePrice::where('commodity_product_id', $data->commodity_product_id)
                ->where('commodity_product_variation_id', $default_variation->id)
                ->where('brand_id', $data->brand_id)
                ->first();
                $default_variation_price = $state_price ? $state_price->price : 0;
            }

        }

        $detail_data['default_variation_price'] = $default_variation_price;
        $detail_data['loading_charge'] = $data->loading_charge;
        $detail_data['insurance_charge'] = $data->insurance_charge;
        $detail_data['quality_charge'] = $data->quality_charge;
        $detail_data['gst'] = $data->gst;

        $extra_charges = 0;
        $other_charges = [];
        foreach ($data->charge_name as $charge_key => $charge_name) {
            $other_charges_arr['name'] = $charge_name;
            $other_charges_arr['price'] = isset($data->charge_price[$charge_key]) ? $data->charge_price[$charge_key] : "0";
            $other_charges_arr['operator'] = isset($data->operator[$charge_key]) ? $data->operator[$charge_key] : "";

            if($other_charges_arr['operator']){
                if($other_charges_arr['operator'] == "+"){
                    $extra_charges += $other_charges_arr['price'];
                }elseif($other_charges_arr['operator'] == "-"){
                    $extra_charges -= $other_charges_arr['price'];
                }elseif($other_charges_arr['operator'] == "*"){
                    $extra_charges += 0;
                }elseif($other_charges_arr['operator'] == "/"){
                    $extra_charges += 0;
                }elseif($other_charges_arr['operator'] == "%"){
                    $extra_charges += 0;
                }
            }
            $other_charges[] = $other_charges_arr;
        }
        $detail_data['other_charges'] = $other_charges;

        $base_price = $data->base_price;
        $gauge_diff = $default_variation_price;
        $all_charges = $data->loading_charge + $data->insurance_charge + $data->quality_charge + $extra_charges;
        $total_amount = $base_price + $gauge_diff + $all_charges;
        $tax_amount = round($total_amount * $data->gst / 100);
        $ex_price = $total_amount + $tax_amount;
        $detail_data['total_charges'] = $all_charges;
        $detail_data['total_amount'] = $total_amount;
        $detail_data['tax_amount'] = $tax_amount;
        $detail_data['ex_price'] = $ex_price;

        $thirty_dates = collect();
        for ($i = 29; $i >= 0; $i--) {
            $thirty_dates->push(Carbon::now()->subDays($i)->format('d/m/y'));
        }

        $price_history = SellerCommodityProductHistory::where('user_id', $data->user_id)
            ->where('commodity_product_id', $data->commodity_product_id)
            ->where('seller_commodity_product_id', $data->id)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->get();


        $packagin_arr = [];
        if($data->packaging_type && count($data->packaging_type) > 0){
            foreach ($data->packaging_type as $key => $type_id) {
                $packagin_type = PackagingType::find($type_id);
                if($packagin_type){
                    $packagin_data['id'] = $packagin_type->id;
                    $packagin_data['name'] = $packagin_type->name;
                    $packagin_data['charge'] = $data->packaging_type_price ? ($data->packaging_type_price && isset($data->packaging_type_price[$packagin_type->id]) ? $data->packaging_type_price[$packagin_type->id] : 0) : 0;
                    $packagin_arr[] = $packagin_data;
                }
            }
        }

        usort($packagin_arr, function ($a, $b) {
            return (float)$a['charge'] <=> (float)$b['charge'];
        });
        $detail_data['packaging_charge'] = $packagin_arr;

        $this->detail = [
            'base_price'                => $detail_data['base_price'],
            'default_variation_price'   => $detail_data['default_variation_price'],
            'loading_charge'            => $detail_data['loading_charge'],
            'insurance_charge'          => $detail_data['insurance_charge'],
            'quality_charge'            => $detail_data['quality_charge'],
            'other_charges'             => $detail_data['other_charges'],
            'total_amount'              => $detail_data['total_amount'],
            'gst'                       => $detail_data['gst'],
            'ex_price'                  => $detail_data['ex_price'],
        ];
    }

    public function closeModal()
    {
        $this->detail = null;
    }
}
