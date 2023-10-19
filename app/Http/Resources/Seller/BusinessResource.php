<?php

namespace App\Http\Resources\Seller;

use App\Models\SellerType;
use App\Models\BusinessType;
use Illuminate\Http\Request;
use App\Models\BusinessCategory;
use Illuminate\Http\Resources\Json\JsonResource;

class BusinessResource extends JsonResource
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
            'name'              => $this->name,
            'about'             => $this->about,
            'category'          => [],
            'type'              => [],
            'seller_type'       => [],
            'is_close'          => $this->is_close,
        ];
        if(!empty($this->category)){
            $business_cat_arr = [];
            foreach($this->category as $cat_id)
            {
                $business_cat = BusinessCategory::find($cat_id);
                $business_cat_data['id'] = $business_cat->id;
                $business_cat_data['name'] = $business_cat->name;
                $business_cat_arr[] = $business_cat_data;
            }
            $data['category'] = $business_cat_arr;
        }

        if(!empty($this->type)){
            $business_type_arr = [];
            foreach($this->type as $type_id)
            {
                $business_type = BusinessType::find($type_id);
                $business_type_data['id'] = $business_type->id;
                $business_type_data['name'] = $business_type->name;
                $business_type_arr[] = $business_type_data;
            }
            $data['type'] = $business_type_arr;
        }

        if(!empty($this->seller_type)){
            $business_seller_type_arr = [];
            foreach($this->seller_type as $seller_type_id)
            {
                $business_seller_type = SellerType::find($seller_type_id);
                $business_seller_type_data['id'] = $business_seller_type->id;
                $business_seller_type_data['name'] = $business_seller_type->name;
                $business_seller_type_arr[] = $business_seller_type_data;
            }
            $data['seller_type'] = $business_type_arr;
        }

        return $data;
    }
}
