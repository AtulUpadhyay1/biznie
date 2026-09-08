<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $business = $this->relationLoaded('getBusiness') ? $this->getBusiness : null;
        $userDetail = $this->relationLoaded('getUserDetail') ? $this->getUserDetail : null;
        $transporterDetail = $this->relationLoaded('getTransporterDetail') ? $this->getTransporterDetail : null;
        $ownerBusiness = $this->relationLoaded('getAddedBy') && $this->getAddedBy?->relationLoaded('getBusiness')
            ? $this->getAddedBy->getBusiness
            : null;
        $companyName = match ($this->type) {
            'seller' => $business->name ?? $ownerBusiness->name ?? $userDetail?->company_name,
            'transporter' => $transporterDetail->company_name ?? $userDetail?->company_name,
            default => $userDetail?->company_name,
        };

        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'company_name' => $companyName,
            'email'      => $this->email,
            'phone'      => $this->phone,
            'type'       => $this->type,
            'is_buyer_and_seller' => $this->resource->isBuyerAndSeller(),
            'role'       => $this->role ?? null,
            'is_staff'   => (bool) ($this->is_staff ?? false),
            'kyc_status' => $this->kyc_status ?? null,
            'status'     => $this->status,
            'created_at' => optional($this->created_at)->toIso8601String(),
        ];
    }
}
