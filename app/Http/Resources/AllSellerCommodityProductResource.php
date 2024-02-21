<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AllSellerCommodityProductResource extends JsonResource
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
            'name'              => $this->name,
            'slug'              => $this->slug,
            'loading_address'   => $this->loading_address ?? [],
            'brands'            => [],
        ];

        if($this->brand_id){
            foreach ($this->brand_id as $brand_id) {

                $brand = getBrand($brand_id);
                $brand_data['id'] = $brand->id;
                $brand_data['name'] = $brand->name;
                $data['brands'][] = $brand_data;

            }
        }

        return $data;
    }
}
