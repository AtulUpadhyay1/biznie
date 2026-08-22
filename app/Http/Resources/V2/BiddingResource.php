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

            /* The live-auction figures. `for_price` is the one anything ranks
               on; the three parts are exposed so the buyer can see the breakup
               behind the doorstep price rather than a single opaque number. */
            'ex_works_price'      => $this->ex_works_price !== null ? (float) $this->ex_works_price : null,
            'freight_charges'     => $this->freight_charges !== null ? (float) $this->freight_charges : null,
            'other_charges'       => $this->other_charges !== null ? (float) $this->other_charges : null,
            'for_price'           => $this->for_price !== null ? (float) $this->for_price : null,
            'ex_works_city'       => $this->ex_works_city,
            'freight_type'        => $this->freight_type,
            'price_source'        => $this->price_source ?: 'app',
            'price_updated_at'    => optional($this->price_updated_at)->toIso8601String(),

            /* Set by the controller when the viewer is not entitled to know who
               quoted: an anonymous rank label ("Offer A") stands in for the
               company name. Absent means the caller may see the seller. */
            'rank'                => $this->rank ?? null,
            'seller_label'        => $this->seller_label ?? null,

            /* Loaded via `getUser.getUserDetail`: `company_name` is a column on
               `user_details`, not on `users`. */
            'seller'              => $this->whenLoaded('getUser', function () {
                $user = $this->getUser;
                if (! $user) {
                    return null;
                }

                return [
                    'id'           => $user->id,
                    'name'         => $user->name,
                    'company_name' => optional($user->getUserDetail)->company_name,
                    'city'         => $this->ex_works_city,
                    'state'        => $this->ex_works_state,
                ];
            }),
            'created_at'          => optional($this->created_at)->toIso8601String(),
            'updated_at'          => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
