<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'slug'        => $this->slug,
            'image'       => $this->image ? imageUrl($this->image) : null,
            'description' => $this->description,
            'tags'        => $this->tags,
            'published_at'=> optional($this->created_at)->toIso8601String(),
        ];
    }
}
