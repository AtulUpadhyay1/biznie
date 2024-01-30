<?php

namespace App\Http\Resources\Customer;

use App\Models\BusinessType;
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
            'name'      => $this->name,
            'type'      => $this->type,
            'email'     => $this->email,
            'phone'     => $this->phone,
            'phone_verified_at' => dateTimeFormat($this->phone_verified_at),
            'user_detail' => NULL,
        ];
        if($this->getUserDetail){
            $getUserDetail = $this->getUserDetail;
            $user_detail['company_name']    = $getUserDetail->company_name;
            $user_detail['type']            = [];

            if(!empty($getUserDetail->type)){
                $type_arr = [];
                foreach($getUserDetail->type as $type_id)
                {
                    $business_type = BusinessType::find($type_id);
                    $business_type_data['id'] = $business_type->id;
                    $business_type_data['name'] = $business_type->name;
                    $type_arr[] = $business_type_data;
                }
                $user_detail['type'] = $type_arr;
            }
            $user_detail['gst_number'] = $getUserDetail->gst_number;
            $user_detail['pan_number'] = $getUserDetail->pan_number;

            $data['user_detail']       = $user_detail;
        }
        return $data;
    }
}
