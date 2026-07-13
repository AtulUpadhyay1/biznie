<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'name'                => $this->name,
            'slug'                => $this->slug,
            'product_category_id' => $this->product_category_id,
            'thumbnail'           => $this->thumbnail ? imageUrl($this->thumbnail) : null,
        ];
    }
}
