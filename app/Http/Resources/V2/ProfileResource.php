<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $detail = $this->whenLoaded('getUserDetail', fn () => $this->getUserDetail);
        $sellerKyc = $this->relationLoaded('getSellerKycDetail') ? $this->getSellerKycDetail : null;
        $transporter = $this->relationLoaded('getTransporterDetail') ? $this->getTransporterDetail : null;
        $business = $this->relationLoaded('getBusiness') ? $this->getBusiness : null;
        $ownerBusiness = $this->relationLoaded('getAddedBy') && $this->getAddedBy?->relationLoaded('getBusiness')
            ? $this->getAddedBy->getBusiness
            : null;
        $resolvedCompanyName = match ($this->type) {
            'seller' => $business->name ?? $ownerBusiness->name ?? $detail?->company_name,
            'transporter' => $transporter->company_name ?? $detail?->company_name,
            default => $detail?->company_name,
        };

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
                'company_name'      => $resolvedCompanyName,
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
            ] : ($resolvedCompanyName ? [
                'company_name'      => $resolvedCompanyName,
                'company_logo'      => null,
                'profile_photo'     => null,
                'company_address'   => null,
                'address_line_one'  => null,
                'address_line_two'  => null,
                'postal_code'       => null,
                'city'              => null,
                'state'             => null,
                'country'           => null,
                'gst_number'        => null,
                'pan_number'        => null,
                'type'              => null,
            ] : null),
            'seller_kyc'          => $sellerKyc ? [
                'status'                => $sellerKyc->status,
                'gst_number'            => $sellerKyc->gst_number,
                'gst_type'              => $sellerKyc->gst_type,
                'identity_type'         => $sellerKyc->identity_type,
                'identity_number'       => $sellerKyc->identity_number,
                'identity_proof'        => $sellerKyc->identity_proof ? imageUrl($sellerKyc->identity_proof) : null,
                'identity_proof_back'   => $sellerKyc->identity_proof_back ? imageUrl($sellerKyc->identity_proof_back) : null,
                'address_proof'         => $sellerKyc->address_proof ? imageUrl($sellerKyc->address_proof) : null,
                'address_proof_back'    => $sellerKyc->address_proof_back ? imageUrl($sellerKyc->address_proof_back) : null,
                'business_registration_certificate' => $sellerKyc->business_registration_certificate
                    ? imageUrl($sellerKyc->business_registration_certificate) : null,
                'address'               => $sellerKyc->address,
                'address_line_one'      => $sellerKyc->address_line_one,
                'address_line_two'      => $sellerKyc->address_line_two,
                'postal_code'           => $sellerKyc->postal_code,
                'city'                  => $sellerKyc->city,
                'state'                 => $sellerKyc->state,
                'country'               => $sellerKyc->country,
                'account_number'        => $sellerKyc->account_number,
                'account_holder_name'   => $sellerKyc->account_holder_name,
                'bank_name'             => $sellerKyc->bank_name,
                'ifsc_code'             => $sellerKyc->ifsc_code,
            ] : null,
            'transporter_detail'  => $transporter ? [
                'company_name'    => $transporter->company_name,
                'gst_number'      => $transporter->gst_number,
                'pan_number'      => $transporter->pan_number,
                'address'         => $transporter->address,
                'alternate_phone' => $transporter->alternate_phone,
                'aadhar_number'   => $transporter->aadhar_number,
            ] : null,
        ];
    }
}
