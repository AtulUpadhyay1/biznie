<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'slug'      => $this->slug,
            'icon'      => $this->icon ? asset('storage/'.$this->icon) : null,
            'thumbnail' => $this->thumbnail ? imageUrl($this->thumbnail) : null,
            'banner'    => $this->banner ? imageUrl($this->banner) : null,
            'featured'  => (bool) $this->featured,
            'meta'      => [
                'title'       => $this->meta_title,
                'description' => $this->meta_description,
                'keywords'    => $this->meta_keywords,
            ],
        ];
    }
}
