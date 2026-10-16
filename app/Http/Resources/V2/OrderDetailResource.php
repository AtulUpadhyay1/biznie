<?php

namespace App\Http\Resources\V2;

use App\Services\Orders\OrderSummary;
use Illuminate\Http\Request;

class OrderDetailResource extends OrderResource
{
    public function toArray(Request $request): array
    {
        $base = parent::toArray($request);
        $addresses = OrderSummary::addresses($this->resource);

        return array_merge($base, [
            // Normalised, render-ready addresses (B28). The raw columns below
            // are kept for older clients only — never print them.
            'billing'           => $addresses['billing'],
            'delivery'          => $addresses['delivery'],
            'delivery_address_missing' => $addresses['delivery'] === null,
            // Quantity per size, GST and terms: the same figures the seller and
            // admin see for this order.
            'summary'           => OrderSummary::forOrder($this->resource),
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
            // Additive: vehicle / driver tracking built from `getDrivers`.
            'tracking'          => $this->resource->trackingRows(),
        ]);
    }
}
