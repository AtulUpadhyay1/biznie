<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Models\BookmarkProduct;
use App\Models\CommodityProductVariation;
use App\Models\CommodityProductStatePrice;
use App\Models\SellerCommodityProductHistory;
use Illuminate\Http\Resources\Json\JsonResource;

class SellerListByCommodityProductResource extends JsonResource
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
            'id'                    => $this->id,
            'commodity_product_id'  => $this->commodity_product_id,
            'name'                  => $this->name,
            'brand'                 => $this->getBrand->name,
            'state'                 => $this->getStatePrice[0]->state,
            'city'                  => $this->getStatePrice[0]->city,
            'base_price'            => $this->base_price,
            'thumbnail'             => $this->getCommodityProduct->thumbnail ? imageUrl($this->getCommodityProduct->thumbnail) : asset('common/images/no-photo.png'),
            'updated_at'            => dateTimeFormat($this->updated_at),
            'price_validity'        => $this->price_validity ? dateTimeFormat($this->price_validity) : null,
            'ex_price'              => 0,
            'default_variation'     => null,
            'unit_name'             => $this->getCommodityProduct?->getUnit?->short_name,
            'quality'               => [],
            'quality_price'         => [],
            'price_history'         => [],
        ];

        $default_variation = CommodityProductVariation::where('commodity_product_id', $this->commodity_product_id)
            ->where('is_default', 1)
            ->first();
        if($this->getCommodityProduct){
            $data['quality'] = $this->getCommodityProduct->quality;
            $data['quality_price'] = $this->getCommodityProduct->quality_price;
        }
        if($default_variation){

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

        $price_history = SellerCommodityProductHistory::where('user_id', $this->user_id)
            ->where('commodity_product_id', $this->commodity_product_id)
            ->where('seller_commodity_product_id', $this->id)
            ->orderBy('created_at', 'desc')
            ->take(2)
            ->get();

        $data['price_history'] = $price_history->map(function ($history) {
            return [
                'base_price' => (string) $history->seller_commodity_product_detail['base_price'],
                'created_at' => dateTimeFormat($history->created_at),
                'updated_at' => dateTimeFormat($history->updated_at),
            ];
        });

        $data['is_bookmarked'] = false;
        $bookmark = BookmarkProduct::where('commodity_product_id', $this->commodity_product_id)
            ->where('seller_commodity_product_id', $this->id)
            ->where('user_id', auth()->id())
            ->first();
        if ($bookmark) {
            $data['is_bookmarked'] = true;
        }
        return $data;
    }
}
