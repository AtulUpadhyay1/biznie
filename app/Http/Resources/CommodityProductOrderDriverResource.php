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
            'tranking_number'               => $this->tranking_number,
        ];

        return $data;
    }
}
