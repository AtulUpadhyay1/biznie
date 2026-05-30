<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'title'      => $this->title,
            'body'       => $this->body,
            'type'       => $this->type,
            'data'       => is_array($this->data) ? $this->data : [],
            'is_read'    => (bool) $this->is_read,
            'created_at' => optional($this->created_at)->toIso8601String(),
        ];
    }
}
