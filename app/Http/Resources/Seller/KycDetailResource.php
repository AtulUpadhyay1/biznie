<?php

namespace App\Http\Resources\Seller;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KycDetailResource extends JsonResource
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
            'account_number'        => $this->account_number,
            'account_holder_name'   => $this->account_holder_name,
            'bank_name'             => $this->bank_name,
            'ifsc_code'             => $this->ifsc_code,
            'bank_status'           => $this->bank_status,
            'address'               => $this->address,
            'postal_code'           => $this->postal_code,
            'city'                  => $this->city,
            'state'                 => $this->state,
            'country'               => $this->country,
            'identity_type'         => $this->identity_type,
            'identity_number'       => $this->identity_number,
            'identity_proof'        => $this->identity_proof ? imageUrl($this->identity_proof) : asset('admin_css/no-photo.png'),
            'identity_proof_back'   => $this->identity_proof_back ? imageUrl($this->identity_proof_back) : asset('admin_css/no-photo.png'),
            'address_type'          => $this->address_type,
            'address_proof'         => $this->address_proof ? imageUrl($this->address_proof) : asset('admin_css/no-photo.png'),
            'address_proof_back'    => $this->address_proof_back ? imageUrl($this->address_proof_back) : asset('admin_css/no-photo.png'),
            'business_registration_certificate' => $this->business_registration_certificate,
            'business_registration_number'      => $this->business_registration_number,
            'trademark_registration_proof'      => $this->trademark_registration_proof ? imageUrl($this->address_proof_back) : asset('admin_css/no-photo.png'),
            'gst_type'              => $this->gst_type,
            'gst_number'            => $this->gst_number,
            'status'                => $this->status,
        ];

        return $data;
    }
}
