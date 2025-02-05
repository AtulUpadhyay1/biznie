<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Models\SellerCommodityProductHistory;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'id'            => $this->id,
            'commodity_product_id'  => $this->commodity_product_id,
            'seller_commodity_product_id'  => $this->seller_commodity_product_id,
            'name'          => $this->getCommodityProduct->name,
            'brand'         => $this->getBrand->name,
            'address'       => $this->city,
            'base_price'    => $this->base_price,
            'transport_price'   => $this->transport_price,
            'commission'        => $this->commission,
            'thumbnail'     => $this->getCommodityProduct->thumbnail ? imageUrl($this->getCommodityProduct->thumbnail) : asset('common/images/no-photo.png'),
            'updated_at'    => dateTimeFormat($this->updated_at),
            'is_mark'       => $this->is_mark ? true : false,
            'price_history' => [],
        ];

        $price_history = SellerCommodityProductHistory::where('user_id', $this->user_id)
            ->where('commodity_product_id', $this->commodity_product_id)
            ->where('seller_commodity_product_id', $this->seller_commodity_product_id)
            ->orderBy('created_at', 'desc')
            ->take(2)
            ->get();

        $data['price_history'] = $price_history->map(function ($history) {
            return [
                'base_price' => (string) $history->seller_commodity_product_detail['base_price'],
                'created_at' => dateTimeFormat($history->created_at),
                'updated_at' => dateTimeFormat($history->updated_at),
            ];
        });

        return $data;
    }
}
