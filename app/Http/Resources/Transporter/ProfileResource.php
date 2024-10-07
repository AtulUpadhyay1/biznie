<?php

namespace App\Http\Resources\Transporter;

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
        // return parent::toArray($request);

        $data = [
            'name'      => $this->name,
            'type'      => $this->type,
            'email'     => $this->email,
            'phone'     => $this->phone,
            'phone_verified_at' => $this->phone_verified_at ? dateTimeFormat($this->phone_verified_at) : '',
            'transporter_detail' => NULL,
        ];
        if($this->getTransporterDetail){
            $getTransporterDetail = $this->getTransporterDetail;
            $user_detail['company_name']    = $getTransporterDetail->company_name;
            $user_detail['gst_number']      = $getTransporterDetail->gst_number;
            $user_detail['address']         = $getTransporterDetail->address;
            $user_detail['alternate_phone'] = $getTransporterDetail->alternate_phone;
            $user_detail['aadhar_number']   = $getTransporterDetail->aadhar_number;

            $data['transporter_detail']     = $user_detail;
        }

        return $data;
    }
}
