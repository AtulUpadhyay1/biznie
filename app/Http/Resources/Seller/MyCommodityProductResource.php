<?php

namespace App\Http\Resources\Seller;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MyCommodityProductResource extends JsonResource
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
            'id'                => $this->id,
            'name'              => $this->name,
            'slug'              => $this->slug,
            'description'       => $this->description,
            'category'          => $this->getCategory ? ['id' => $this->category_id , 'name' => $this->getCategory->name] : '',
            'sub_category'      => $this->getSubCategory ? ['id' => $this->sub_category_id,'name' => $this->getSubCategory->name] : '',
            'sub_sub_category'  => $this->getSubSubCategory ? ['id' => $this->sub_sub_category_id,'name' => $this->getSubSubCategory->name] : '',
            'unit'              => $this->getUnit ? ['id' => $this->unit_id,'name' => $this->getUnit->name] : '',
            'brands'            => [],
            'packaging'         => [],
            'thumbnail'         => imageUrl($this->thumbnail),
            'images'            => [],
        ];
        if($this->brand_id){
            foreach ($this->brand_id as $brand_id) {

                $brand = getBrand($brand_id);
                $brand_data['id'] = $brand->id;
                $brand_data['name'] = $brand->name;
                $data['brands'][] = $brand_data;

            }
        }
        if($this->packaging_type){
            foreach ($this->packaging_type as $key => $packaging_type_id) {

                $packaging_type = getPackagingType($packaging_type_id);
                $packaging_type_data['id'] = $packaging_type->id;
                $packaging_type_data['name'] = $packaging_type->name;
                $packaging_type_data['price'] = (int)$this->packaging_type_price[$packaging_type_id];
                $data['packaging'][] = $packaging_type_data;

            }
        }

        if($this->images && $this->images != ""){
            foreach ($this->images as $images) {
                $data['images'][] = imageUrl($images);
            }
        }


        return $data;
    }
}
