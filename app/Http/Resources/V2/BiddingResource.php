<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BiddingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'product_enquiry_id'  => $this->product_enquiries_id,
            'unique_id'           => $this->unique_id,
            'status'              => $this->status,
            'is_marked'           => (bool) $this->is_mark,
            'base_price'          => $this->base_price,
            'transport_price'     => $this->transport_price,
            'commission'          => $this->commission,
            'commission_type'     => $this->commission_type ?? null,
            'price'               => $this->price,
            'value'               => $this->value,
            'loading_address'     => $this->loading_address,
            'delivery_by'         => $this->delivery_by,
            'description'         => $this->description,
            'message'             => $this->message,
            'price_validity'      => $this->price_validity,
            'load_within'         => $this->load_within,
            'credit_days'         => $this->credit_days,
            'gst'                 => $this->gst,
            'required_booking_amount' => $this->required_booking_amount,
            'ex_price'            => $this->ex_price,
            'for_price'           => $this->for_price,
            'seller'              => $this->whenLoaded('getUser', function () {
                $user = $this->getUser;
                return $user ? [
                    'id'           => $user->id,
                    'name'         => $user->name,
                    'company_name' => $user->company_name ?? null,
                    'city'         => $user->city ?? null,
                    'state'        => $user->state ?? null,
                ] : null;
            }),
            'created_at'          => optional($this->created_at)->toIso8601String(),
            'updated_at'          => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
