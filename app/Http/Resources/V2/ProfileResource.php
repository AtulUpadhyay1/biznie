<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $detail = $this->whenLoaded('getUserDetail', fn () => $this->getUserDetail);

        return [
            'id'                  => $this->id,
            'name'                => $this->name,
            'email'               => $this->email,
            'phone'               => $this->phone,
            'type'                => $this->type,
            'status'              => $this->status,
            'kyc_status'          => $this->kyc_status ?? null,
            'kyc_verified_at'     => optional($this->kyc_verified_at)->toIso8601String(),
            'is_staff'            => (bool) ($this->is_staff ?? false),
            'role'                => $this->role ?? null,
            'cash_balance'        => (float) ($this->cash_balance ?? 0),
            'credit_balance'      => (float) ($this->credit_balance ?? 0),
            'assign_credit_balance' => (float) ($this->assign_credit_balance ?? 0),
            'credit_days'         => $this->credit_days ?? null,
            'created_at'          => optional($this->created_at)->toIso8601String(),
            'user_detail'         => $detail ? [
                'company_name'      => $detail->company_name,
                'company_logo'      => $detail->company_logo ? imageUrl($detail->company_logo) : null,
                'profile_photo'     => $detail->profile_photo ? imageUrl($detail->profile_photo) : null,
                'company_address'   => $detail->company_address,
                'address_line_one'  => $detail->address_line_one,
                'address_line_two'  => $detail->address_line_two,
                'postal_code'       => $detail->postal_code,
                'city'              => $detail->city,
                'state'             => $detail->state,
                'country'           => $detail->country,
                'gst_number'        => $detail->gst_number,
                'pan_number'        => $detail->pan_number,
                'type'              => $detail->type,
            ] : null,
        ];
    }
}
