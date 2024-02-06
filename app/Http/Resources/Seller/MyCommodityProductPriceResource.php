<?php

namespace App\Http\Resources\Seller;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MyCommodityProductPriceResource extends JsonResource
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
            'id'                => $this->id,
            'base_price'        => $this->base_price,
            'loading_charge'    => $this->loading_charge,
            'insurance_charge'  => $this->insurance_charge,
            'quality_charge'    => $this->quality_charge,
            'gst'               => $this->gst,
            'tcs'               => $this->tcs,
            'charges'           => [],
        ];
        if($this->charge_name){
            foreach($this->charge_name as $key => $charge_name)
            {
                $charge_data['charge_name']     = $charge_name;
                $charge_data['charge_price']    = $this->charge_price[$key];
                $charge_data['operator']        = $this->operator[$key];
                $data['charges'][] = $charge_data;
            }
        }
        return $data;
    }
}
