<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'email'      => $this->email,
            'phone'      => $this->phone,
            'type'       => $this->type,
            'role'       => $this->role ?? null,
            'is_staff'   => (bool) ($this->is_staff ?? false),
            'kyc_status' => $this->kyc_status ?? null,
            'status'     => $this->status,
            'created_at' => optional($this->created_at)->toIso8601String(),
        ];
    }
}
