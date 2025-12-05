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
            'credit_availability' => null,
            'business_details'  => null,
            'kyc_details'       => null,
            'status'            => $this->status,
            'block_reason'      => $this->block_reason ?? null,
            'is_staff'          => (int) $this->is_staff,
            'role'              => $this->role ?? null,
            'permission'        => $this->permission ? $this->permission : [],
        ];

        if($this->is_staff){
            if($this->getAddedBy->getBusiness){
                $data['business_details'] = new BusinessResource($this->getAddedBy->getBusiness);
            }
            if($this->getAddedBy->getSellerKycDetail){
                $data['kyc_details'] = new KycDetailResource($this->getAddedBy->getSellerKycDetail);
            }
            $data['credit_availability'] = $this->getAddedBy->credit_availability ? true : false;
        }else{
            $data['business_details']  = $this->getBusiness ? new BusinessResource($this->getBusiness) : null;
            $data['kyc_details']       = $this->getSellerKycDetail ? new KycDetailResource($this->getSellerKycDetail) : null;
            $data['credit_availability'] = $this->credit_availability ? true : false;
        }
        return $data;
    }
}
