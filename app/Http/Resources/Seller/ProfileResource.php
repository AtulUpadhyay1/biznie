<?php

namespace App\Http\Resources\Seller;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //return parent::toArray($request);
        $data = [
            'name'              => $this->name,
            'type'              => $this->type,
            'email'             => $this->email,
            'phone'             => $this->phone,
            'phone_verified_at' => dateTimeFormat($this->phone_verified_at),
            'kyc_status'        => $this->getSellerKycDetail ? $this->getSellerKycDetail->status : null,
        ];
        return $data;
    }
}
