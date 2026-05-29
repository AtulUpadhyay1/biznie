<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $images = [];
        if (is_array($this->images)) {
            foreach ($this->images as $img) {
                $images[] = imageUrl($img);
            }
        }

        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'slug'        => $this->slug,
            'description' => $this->description,
            'thumbnail'   => $this->thumbnail ? imageUrl($this->thumbnail) : null,
            'images'      => $images,
            'base_price'  => (float) ($this->base_price ?? 0),
            'gst'         => (float) ($this->gst ?? 0),
            'category'    => $this->whenLoaded('getCategory', fn () => $this->getCategory ? [
                'id'   => $this->getCategory->id,
                'name' => $this->getCategory->name,
            ] : null),
            'sub_category' => $this->whenLoaded('getSubCategory', fn () => $this->getSubCategory ? [
                'id'   => $this->getSubCategory->id,
                'name' => $this->getSubCategory->name,
            ] : null),
            'unit'        => $this->whenLoaded('getUnit', fn () => $this->getUnit ? [
                'id'   => $this->getUnit->id,
                'name' => $this->getUnit->name,
            ] : null),
            'status'      => $this->status,
        ];
    }
}
