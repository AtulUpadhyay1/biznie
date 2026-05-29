<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'pincode'          => $this->pincode,
            'address_line_one' => $this->address_line_one,
            'address_line_two' => $this->address_line_two,
            'city'             => $this->city,
            'state'            => $this->state,
            'country'          => $this->country ?: 'India',
            'company_name'     => $this->company_name,
            'phone'            => $this->phone,
            'gst'              => $this->gst,
            'created_at'       => optional($this->created_at)->toIso8601String(),
        ];
    }
}
