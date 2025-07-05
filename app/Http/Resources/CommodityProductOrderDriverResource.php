<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommodityProductOrderDriverResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);

        $data = [
            'id'        => $this->id,
            'name'      => $this->name,
            'phone'     => $this->phone,
            'photo'     => imageUrl($this->photo),
            'unloaded_vehicle_photo'        => imageUrl($this->unloaded_vehicle_photo),
            'loaded_vehicle_photo'          => imageUrl($this->loaded_vehicle_photo),
            'driver_with_vehicle_photo'     => imageUrl($this->driver_with_vehicle_photo),
            'vehicle_number'                => $this->vehicle_number,
            'tracking_number'               => $this->tracking_number,
            'invoice'                       => imageUrl($this->invoice),
            'ebill'                         => imageUrl($this->ebill),
            'ebill_expiry_date'             => $this->ebill_expiry_date ? dateFormat($this->ebill_expiry_date) : null,
            'transport_receipt'             => imageUrl($this->transport_receipt),
            'alternate_phone_number'        => $this->alternate_phone_number,
            'transporter_name'              => $this->transporter_name,
            'transporter_phone_number'      => $this->transporter_phone_number,
            'advance_amount'                => $this->advance_amount,
            'final_quantity_by_seller'      => $this->final_quantity_by_seller ? array_sum($this->final_quantity_by_seller) : 0,
            'final_quantity_by_customer'    => $this->final_quantity_by_customer,
        ];

        return $data;
    }
}
