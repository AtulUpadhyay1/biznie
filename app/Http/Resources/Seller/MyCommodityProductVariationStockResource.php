<?php

namespace App\Http\Resources\Seller;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MyCommodityProductVariationStockResource extends JsonResource
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
            'stock'         => $this->stock ?? 0,
            'is_selected'   => $this->is_selected,
        ];

        return $data;
    }
}
