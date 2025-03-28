<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\BookmarkProduct;
use App\Models\TransporterDetail;
use Illuminate\Support\Facades\DB;
use App\Models\CommodityProductState;
use App\Models\TransporterAddressPrice;
use App\Models\CommodityProductVariation;
use App\Models\CommodityProductStatePrice;
use App\Models\SellerCommodityProductHistory;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Seller\MyCommodityProductVariationResource;

class ProductDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);

        $data = [
            'id'                            => $this->id,
            'commodity_product_id'          => $this->commodity_product_id,
            'seller_commodity_product_id'   => $this->id,
            'name'                          => $this->name,
            'description'                   => $this->description,
            'brand'                         => ['id' => $this->getBrand->id, 'name' => $this->getBrand->name],
            'unit'                          => ['id' => $this->getCommodityProduct->getUnit->id, 'name' => $this->getCommodityProduct->getUnit->name],
            'address'                       => isset($this->getStatePrice[0]) ? $this->getStatePrice[0]->city : null,
            'base_price'                    => $this->base_price,
            'thumbnail'                     => $this->thumbnail ? imageUrl($this->thumbnail) : asset('common/images/no-photo.png'),
            'images'                        => [],
            'loading_charge'                => $this->loading_charge,
            'loading_position'              => $this->loading_position,
            'price_validity'                => $this->price_validity ? dateTimeFormat($this->price_validity) : null,
            'insurance_charge'              => $this->insurance_charge,
            'quality_charge'                => $this->quality_charge,
            'gst'                           => $this->gst,
            'tcs'                           => $this->tcs,
            'min_order_qty'                 => 0,
            'order_amount_type'             => $this->order_amount_type,
            'required_order_amount'         => $this->required_order_amount,
            'charts'                        => [],
            'charges'                       => [],
            'variation'                     => MyCommodityProductVariationResource::collection($this->getStatePrice),
            'ex_price'                      => 0,
            'freight_price'                 => 0,
            'default_variation'             => [],
        ];
        if($this->getCommodityProduct){
            $data['min_order_qty'] = $this->getCommodityProduct->min_order_qty;
            $data['last_updated'] = dateTimeFormat($this->updated_at);

            $data['quality'] = $this->getCommodityProduct->quality;
            $data['quality_price'] = $this->getCommodityProduct->quality_price;
        }
        $transporters_ids = TransporterDetail::whereJsonContains('commodity_product', $this->commodity_product_id)
            ->pluck('user_id');

        $userDetail = auth()->user()->getUserDetail;
        if ($userDetail && $userDetail->state && $userDetail->city) {
            $transporter_address_price = TransporterAddressPrice::whereIn('user_id', $transporters_ids)
            ->where('state', $userDetail->state)
            ->where('city', $userDetail->city)
            ->orderBy('min_price', 'asc')
            ->first();

            if ($transporter_address_price) {
                $data['freight_price'] = (int) $transporter_address_price->min_price;
            }
        }
        $product_state = CommodityProductState::where('commodity_product_id', $this->commodity_product_id)->where('brand_id', $this->brand_id)->where('city', $this->city)->first();
        if ($product_state && $product_state->chart) {
            foreach ($product_state->chart ?? [] as $chart) {
                $data['charts'][] = imageUrl($chart);
            }
        }

        if($this->images && $this->images != ""){
            foreach ($this->images as $images) {
                $data['images'][]           = imageUrl($images);
            }
        }

        $default_variation = CommodityProductVariation::where('commodity_product_id', $this->commodity_product_id)
            ->where('is_default', 1)
            ->first();

        if($default_variation){
            // $data['default_variation'] = $default_variation;

            foreach($default_variation->value as $value){
                $value['unit']  = null;

                if($this->getCommodityProduct){
                    $commodity = $this->getCommodityProduct;
                    if($commodity && $commodity->unit){
                        $value['unit']['name'] = getProductUnit($commodity->unit[$value['name']]) ? getProductUnit($commodity->unit[$value['name']])->name : '';
                        $value['unit']['short_name'] = getProductUnit($commodity->unit[$value['name']]) ? getProductUnit($commodity->unit[$value['name']])->short_name : '';
                    }
                }

                $data['default_variation'][] = $value;
            }

            if($default_variation){
                $state_price = CommodityProductStatePrice::where('commodity_product_id', $this->commodity_product_id)
                ->where('commodity_product_variation_id', $default_variation->id)
                ->where('brand_id', $this->brand_id)
                ->first();

                if($state_price){
                    $commodityProduct = $this->getCommodityProduct;
                    $ex_price = $state_price->price + $this->base_price + $commodityProduct->loading_charge + $commodityProduct->insurance_charge + $commodityProduct->quality_charge;
                    $extra_charges = 0;
                    foreach ($commodityProduct->charge_name as $charge_key => $charge_name) {
                        $other_charges_arr['name'] = $charge_name;
                        $other_charges_arr['price'] = isset($commodityProduct->charge_price[$charge_key]) ? $commodityProduct->charge_price[$charge_key] : 0;
                        $other_charges_arr['operator'] = isset($commodityProduct->operator[$charge_key]) ? $commodityProduct->operator[$charge_key] : "";

                        if($other_charges_arr['operator']){
                            if($other_charges_arr['operator'] == "+"){
                                $extra_charges += $other_charges_arr['price'];
                            }elseif($other_charges_arr['operator'] == "-"){
                                $extra_charges -= $other_charges_arr['price'];
                            }elseif($other_charges_arr['operator'] == "*"){
                                $extra_charges += $ex_price * $other_charges_arr['price'];
                            }elseif($other_charges_arr['operator'] == "/"){
                                $extra_charges += $ex_price / $other_charges_arr['price'];
                            }elseif($other_charges_arr['operator'] == "%"){
                                $extra_charges += $ex_price * ($other_charges_arr['price'] / 100);
                            }
                        }
                    }

                    $tax_amount = ($ex_price + $extra_charges) * $commodityProduct->gst / 100;

                    $data['ex_price'] = round($ex_price + $extra_charges + $tax_amount);
                }

            }

        }

        $thirty_dates = collect();
        for ($i = 29; $i >= 0; $i--) {
            $thirty_dates->push(Carbon::now()->subDays($i)->format('d/m/y'));
        }

        $price_history = SellerCommodityProductHistory::where('user_id', $this->user_id)
            ->where('commodity_product_id', $this->commodity_product_id)
            ->where('seller_commodity_product_id', $this->id)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->get();

            $last_thirty_days_calls = $thirty_dates->mapWithKeys(function ($date) use ($price_history) {

                $filtered_records = $price_history->filter(function ($history) use ($date) {
                    return Carbon::parse($history->created_at)->format('d/m/y') === $date;
                });

                $average_base_price = $filtered_records->last(function ($history) {
                    return isset($history->seller_commodity_product_detail['base_price']) ? $history->seller_commodity_product_detail['base_price'] : $this->base_price;
                });

                return [$date => round($average_base_price)];
            });

        $data['price_history'] = $last_thirty_days_calls;
        $data['test'] = [
            $this->user_id,
            $this->commodity_product_id,
            $this->id,
        ];
        $data['product_delivery_info'] = websiteSetupValue('product_delivery_info');
        $data['product_terms_condition'] = websiteSetupValue('product_terms_condition');

        // $data['price_history'] = $price_history->map(function ($history) {
        //     return [
        //         'base_price' => (string) $history->seller_commodity_product_detail['base_price'],
        //         'created_at' => dateTimeFormat($history->created_at),
        //         'updated_at' => dateTimeFormat($history->updated_at),
        //     ];
        // });
        $data['is_bookmarked'] = false;
        $bookmark = BookmarkProduct::where('commodity_product_id', $this->commodity_product_id)
            ->where('seller_commodity_product_id', $this->id)
            ->where('user_id', auth()->id())
            ->first();
        if ($bookmark) {
            $data['is_bookmarked'] = true;
        }
        return $data;

        // $data = [
        //     'id'                            => $this->id,
        //     'commodity_product_id'          => $this->commodity_product_id,
        //     'seller_commodity_product_id'   => $this->seller_commodity_product_id,
        //     'name'                          => $this->getSellerCommodityProduct->name,
        //     'description'                   => $this->getSellerCommodityProduct->description,
        //     'brand'                         => ['id' => $this->getBrand->id, 'name' => $this->getBrand->name],
        //     'unit'                          => ['id' => $this->getCommodityProduct->getUnit->id, 'name' => $this->getCommodityProduct->getUnit->name],
        //     'address'                       => $this->city,
        //     'base_price'                    => $this->base_price,
        //     'thumbnail'                     => $this->getSellerCommodityProduct->thumbnail ? imageUrl($this->getSellerCommodityProduct->thumbnail) : asset('common/images/no-photo.png'),
        //     'images'                        => [],
        //     'base_price'                    => $this->base_price,
        //     'loading_charge'                => $this->loading_charge,
        //     'loading_position'              => $this->getSellerCommodityProduct->loading_position,
        //     'price_validity'                => $this->getSellerCommodityProduct->price_validity ? dateTimeFormat($this->getSellerCommodityProduct->price_validity) : null,
        //     'insurance_charge'              => $this->insurance_charge,
        //     'quality_charge'                => $this->quality_charge,
        //     'gst'                           => $this->gst,
        //     'tcs'                           => $this->tcs,
        //     'min_order_qty'                 => $this->min_order_qty,
        //     'order_amount_type'             => $this->order_amount_type,
        //     'required_order_amount'         => $this->required_order_amount,
        //     'charts'                        => [],
        //     'charges'                       => [],
        //     'variation'                     => MyCommodityProductVariationResource::collection($this->getSellerStatePrice),
        //     'default_variation'             => [],
        // ];

        // $product_state = CommodityProductState::where('commodity_product_id', $this->commodity_product_id)->where('brand_id', $this->brand_id)->where('city', $this->city)->first();
        // if ($product_state && $product_state->chart) {
        //     foreach ($product_state->chart ?? [] as $chart) {
        //         $data['charts'][] = imageUrl($chart);
        //     }
        // }

        // $product = $this->getSellerCommodityProduct;

        // if($product->images && $product->images != ""){
        //     foreach ($product->images as $images) {
        //         $data['images'][]           = imageUrl($images);
        //     }
        // }

        // return $data;

        // if($product->charge_name){
        //     foreach($product->charge_name as $key => $charge_name){
        //         $charge_data['charge_name'] = $charge_name;
        //         $charge_data['charge_price']= $product->charge_price[$key];
        //         $charge_data['operator']    = $product->operator[$key];
        //         $data['charges'][]          = $charge_data;
        //     }
        // }

        // if($product->attributes && count($product->attributes) > 0 && $product->variation && count($product->variation) > 0){
        //     foreach ($product->attributes as $key => $attribute_id) {
        //         $variation_arr['name']  = getAttribute($attribute_id)->name;
        //         $variation_arr['value'] = $product->variation[getAttribute($attribute_id)->name];
        //         $variation_arr['unit']  = null;
        //         if($product->unit && $product->unit[getAttribute($attribute_id)->name]){
        //             $variation_arr['unit']['name'] = getProductUnit($product->unit[getAttribute($attribute_id)->name])->name;
        //             $variation_arr['unit']['short_name'] = getProductUnit($product->unit[getAttribute($attribute_id)->name])->short_name;
        //         }

        //         $data['variation'][] = $variation_arr;
        //     }

        //     $variation_arr['name']  = 'Price';
        //     $variation_arr['value'] = $product->variation['Price'];
        //     $variation_arr['unit']  = null;
        //     $data['variation'][] = $variation_arr;
        // }

        // return $data;
    }
}
