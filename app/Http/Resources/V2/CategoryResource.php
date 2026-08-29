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
            // The icon column also holds markup-style icons (e.g. an <i class="bi ...">)
            // on older rows; only turn real upload paths into URLs.
            'icon'      => $this->iconUrl(),
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

    private function iconUrl(): ?string
    {
        $icon = trim((string) $this->icon);

        if ($icon === '' || str_contains($icon, '<')) {
            return null;
        }

        return asset('storage/'.ltrim($icon, '/'));
    }
}
