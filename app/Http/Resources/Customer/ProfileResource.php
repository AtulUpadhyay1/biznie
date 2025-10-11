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
            'credit_availability' => $this->credit_availability ? true : false,
            'kyc_status' => $this->kyc_status,
            'kyc_verified_at' => dateTimeFormat($this->kyc_verified_at),
            'kyc_description' => $this->kyc_description,
            'user_detail' => NULL,
            'is_staff'          => (int) $this->is_staff,
            'permission'        => $this->permission ? $this->permission : [],
        ];
        $getUserDetail = $this->getUserDetail;
        if($getUserDetail){
            $user_detail['profile_photo']   = $getUserDetail->profile_photo ? imageUrl($getUserDetail->profile_photo) : asset('common/images/no-photo.png');
            $user_detail['company_name']    = $getUserDetail->company_name;
            $user_detail['company_logo']    = $getUserDetail->company_logo ? imageUrl($getUserDetail->company_logo) : asset('common/images/no-photo.png');
            $user_detail['company_address'] = $getUserDetail->company_address;
            $user_detail['state']           = $getUserDetail->state;
            $user_detail['city']            = $getUserDetail->city;
            $user_detail['type']            = [];

            if(!empty($getUserDetail->type) && count($getUserDetail->type) > 0){
                $type_arr = [];
                foreach($getUserDetail->type as $type_id){
                    $business_type = BusinessType::find($type_id);
                    if($business_type){
                        $business_type_data['id'] = $business_type->id;
                        $business_type_data['name'] = $business_type->name;
                        $type_arr[] = $business_type_data;
                    }
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
