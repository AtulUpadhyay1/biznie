<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Request;

class OrderDetailResource extends OrderResource
{
    public function toArray(Request $request): array
    {
        $base = parent::toArray($request);

        return array_merge($base, [
            'billing_address'   => $this->billing_address,
            'delivery_address'  => $this->delivery_address,
            'consignee_detail'  => $this->consignee_detail,
            'loading_address'   => $this->loading_address,
            'purpose'           => $this->purpose,
            'description'       => $this->description,
            'message'           => $this->message,
            'price'             => $this->price,
            'token_amount'      => (float) ($this->token_amount ?? 0),
            'transport_price'   => (float) ($this->transport_price ?? 0),
            'commission'        => $this->commission,
            'history'           => $this->history,
            'invoice'           => $this->invoice ? imageUrl($this->invoice) : null,
            'final_invoice'     => $this->final_invoice ? imageUrl($this->final_invoice) : null,
            'e_bill'            => $this->e_bill ? imageUrl($this->e_bill) : null,
            'quality_check_certificate' => $this->quality_check_certificate ? imageUrl($this->quality_check_certificate) : null,
            'insurance_certificate'     => $this->insurance_certificate ? imageUrl($this->insurance_certificate) : null,
            'final_quantity_by_seller'   => $this->final_quantity_by_seller,
            'final_quantity_by_customer' => $this->final_quantity_by_customer,
            'cancel_reason'     => $this->cancel_reason,
        ]);
    }
}
