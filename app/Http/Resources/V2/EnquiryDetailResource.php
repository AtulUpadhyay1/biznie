<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Request;

class EnquiryDetailResource extends EnquiryResource
{
    public function toArray(Request $request): array
    {
        $base = parent::toArray($request);

        return array_merge($base, [
            'variation'         => $this->variation,
            'billing_address'   => $this->billing_address,
            'delivery_address'  => $this->delivery_address,
            'consignee_detail'  => $this->consignee_detail,
            'purpose'           => $this->purpose,
            'description'       => $this->description,
            'message'           => $this->message,
            'quality'           => $this->quality,
            'packaging_charge'  => $this->packaging_charge,
            'history'           => $this->history,
        ]);
    }
}
