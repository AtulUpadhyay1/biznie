<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'name'          => $this->getCommodityProduct->name,
            'brand'         => $this->getBrand->name,
            'address'       => $this->city,
            'base_price'    => $this->base_price,
            'thumbnail'     => $this->thumbnail ? imageUrl($this->thumbnail) : asset('common/images/no-photo.png'),
            'updated_at'    => dateTimeFormat($this->updated_at),
        ];

        return $data;
    }
}
