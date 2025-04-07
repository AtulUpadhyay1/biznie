<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
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
            'name'  => $this->name,
            'type'  => $this->type,
            'phone' => $this->phone,
            'email' => $this->email,
            'company_name' => null,
        ];
        if($this->type == 'seller'){
            if($this->getBusiness){
                $data['company_name'] = $this->getBusiness->name;
            }
        }
        if($this->type == 'transporter'){
            if($this->getTransporterDetail){
                $data['company_name'] = $this->getTransporterDetail->company_name;
            }
        }
        if($this->type == 'customer'){
            if($this->getUserDetail){
                $data['company_name'] = $this->getUserDetail->company_name;
            }
        }
        return $data;
    }
}
