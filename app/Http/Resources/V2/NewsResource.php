<?php

namespace App\Http\Resources\V2;

use App\Support\NewsText;
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
            // Clean text for cards — no tags, emoji or "⸻" rules (B19).
            'excerpt'         => NewsText::excerpt($this->description),
            'reading_minutes' => NewsText::readingMinutes($this->description),
            // The article page only: the body as safe HTML.
            'body_html'       => $this->when($request->route('slug') !== null, fn () => NewsText::bodyHtml($this->description)),
            'tags'        => $this->tags,
            'published_at'=> optional($this->created_at)->toIso8601String(),
        ];
    }
}
