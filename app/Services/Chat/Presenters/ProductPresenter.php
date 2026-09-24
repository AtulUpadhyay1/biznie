<?php

namespace App\Services\Chat\Presenters;

use App\Models\CommodityProduct;

/** CommodityProduct → ChatProduct (plan §4.3). */
class ProductPresenter
{
    public const RELATIONS = ['getCategory:id,name', 'getUnit:id,name'];

    public function present(CommodityProduct $product): array
    {
        return [
            'id'        => (int) $product->id,
            'name'      => (string) $product->name,
            'slug'      => (string) $product->slug,
            'thumbnail' => $product->thumbnail ? (imageUrl($product->thumbnail) ?: null) : null,
            'category'  => $product->getCategory?->name,
            'unit'      => $product->getUnit?->name,
            'href'      => '/products/' . $product->slug,
        ];
    }
}
