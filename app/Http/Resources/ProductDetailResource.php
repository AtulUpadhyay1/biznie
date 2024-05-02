<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
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
            'seller_commodity_product_id'   => $this->seller_commodity_product_id,
            'name'                          => $this->getSellerCommodityProduct->name,
            'description'                   => $this->getSellerCommodityProduct->description,
            'brand'                         => ['id' => $this->getBrand->id, 'name' => $this->getBrand->name],
            'unit'                          => ['id' => $this->getSellerCommodityProduct->getUnit->id, 'name' => $this->getSellerCommodityProduct->getUnit->name],
            'address'                       => $this->city,
            'base_price'                    => $this->base_price,
            'thumbnail'                     => $this->thumbnail ? imageUrl($this->thumbnail) : asset('common/images/no-photo.png'),
            'images'                        => [],
            'base_price'                    => $this->base_price,
            'loading_charge'                => $this->loading_charge,
            'insurance_charge'              => $this->insurance_charge,
            'quality_charge'                => $this->quality_charge,
            'gst'                           => $this->gst,
            'tcs'                           => $this->tcs,
            'charges'                       => [],
            'variation'                     => MyCommodityProductVariationResource::collection($this->getSellerStatePrice)
        ];

        return $data;

        // $product = $this->getSellerCommodityProduct;

        // if($product->images && $product->images != ""){
        //     foreach ($product->images as $images) {
        //         $data['images'][]           = imageUrl($images);
        //     }
        // }

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
