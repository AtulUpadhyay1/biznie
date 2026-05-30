<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WatchlistResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $cp  = $this->getCommodityProduct;
        $scp = $this->getSellerCommodityProduct;

        return [
            'id'                          => $this->id,
            'commodity_product_id'        => $this->commodity_product_id,
            'seller_commodity_product_id' => $this->seller_commodity_product_id,
            'product' => $cp ? [
                'id'        => $cp->id,
                'name'      => $cp->name,
                'slug'      => $cp->slug ?? null,
                'thumbnail' => $cp->thumbnail ? imageUrl($cp->thumbnail) : null,
                'category'  => $cp->getCategory ? [
                    'id'   => $cp->getCategory->id,
                    'name' => $cp->getCategory->name,
                ] : null,
                'unit' => $cp->getUnit ? [
                    'id'   => $cp->getUnit->id,
                    'name' => $cp->getUnit->name,
                ] : null,
            ] : null,
            'seller_product' => $scp ? [
                'id'    => $scp->id,
                'price' => $scp->price ?? null,
                'brand' => $scp->getBrand ? [
                    'id'   => $scp->getBrand->id,
                    'name' => $scp->getBrand->name,
                ] : null,
                'seller' => $scp->getUser ? [
                    'id'           => $scp->getUser->id,
                    'name'         => $scp->getUser->name,
                    'company_name' => optional($scp->getUser->getUserDetail ?? null)->company_name,
                ] : null,
            ] : null,
            'created_at' => optional($this->created_at)->toIso8601String(),
        ];
    }
}
