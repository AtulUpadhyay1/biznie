<?php

namespace App\Http\Resources\Seller;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MyCommodityProductVariationResource extends JsonResource
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
            'id'            => $this->id,
            'value'         => $this->value,
            'price'         => $this->price,
            'is_selected'   => $this->is_selected,
        ];

        return $data;

        // $data = [
        //     'variation' => []
        // ];

        // if($this->attributes && count($this->attributes) > 0 && $this->variation && count($this->variation) > 0){
        //     foreach ($this->attributes as $key => $attribute_id) {
        //         $variation_arr['name']  = getAttribute($attribute_id)->name;
        //         $variation_arr['value'] = $this->variation[getAttribute($attribute_id)->name];
        //         $variation_arr['unit']  = null;
        //         if($this->unit && $this->unit[getAttribute($attribute_id)->name]){
        //             $variation_arr['unit']['name'] = getProductUnit($this->unit[getAttribute($attribute_id)->name])->name;
        //             $variation_arr['unit']['short_name'] = getProductUnit($this->unit[getAttribute($attribute_id)->name])->short_name;
        //         }

        //         $data['variation'][] = $variation_arr;
        //     }

        //     $variation_arr['name']  = 'Price';
        //     $variation_arr['value'] = $this->variation['Price'];
        //     $variation_arr['unit']  = null;
        //     $data['variation'][] = $variation_arr;

        // }
        // return $data;
    }
}
