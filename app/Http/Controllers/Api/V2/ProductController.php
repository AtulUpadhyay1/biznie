<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\ProductDetailResource;
use App\Http\Resources\V2\ProductOfferResource;
use App\Http\Resources\V2\ProductResource;
use App\Models\CommodityProduct;
use App\Services\ProductPricingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $q = CommodityProduct::active()
            ->with(['getCategory', 'getSubCategory', 'getUnit'])
            ->latest();

        if ($search = $request->string('search')->toString()) {
            $q->search($search);
        }
        if ($categoryId = $request->integer('category_id')) {
            $q->where('category_id', $categoryId);
        }
        if ($subCategoryId = $request->integer('sub_category_id')) {
            $q->where('sub_category_id', $subCategoryId);
        }

        $list = $q->paginate($request->integer('per_page', 20));

        return response()->json([
            'success' => true,
            'data'    => ProductResource::collection($list),
            'meta'    => [
                'current_page' => $list->currentPage(),
                'last_page'    => $list->lastPage(),
                'per_page'     => $list->perPage(),
                'total'        => $list->total(),
            ],
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $product = CommodityProduct::active()
            ->with(['getCategory', 'getSubCategory', 'getSubSubCategory', 'getUnit'])
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data'    => new ProductDetailResource($product),
        ]);
    }

    /**
     * Live seller offers for a commodity product, cheapest first.
     *
     * This is the step between the catalog and the detail page: the buyer picks
     * which seller's offer to open.
     */
    public function offers(string $slug, Request $request, ProductPricingService $pricing): JsonResponse
    {
        $product = CommodityProduct::active()
            ->with(['getCategory', 'getUnit'])
            ->where('slug', $slug)
            ->firstOrFail();

        $offers = $pricing->sellerOffers($product);

        return response()->json([
            'success' => true,
            'data'    => [
                'product' => [
                    'id'        => $product->id,
                    'name'      => $product->name,
                    'slug'      => $product->slug,
                    'thumbnail' => $product->thumbnail ? imageUrl($product->thumbnail) : null,
                    'category'  => $product->getCategory ? [
                        'id'   => $product->getCategory->id,
                        'name' => $product->getCategory->name,
                    ] : null,
                    'unit'      => $product->getUnit ? [
                        'id'   => $product->getUnit->id,
                        'name' => $product->getUnit->name,
                    ] : null,
                    'min_order_qty' => (float) ($product->min_order_qty ?? 0),
                ],
                'destinations' => $pricing->deliveryDestinations((int) $product->id),
                'offers'       => $this->cheapestFirst(ProductOfferResource::collection($offers)->resolve()),
            ],
        ]);
    }

    /**
     * Order offers by the price the buyer actually pays at their door.
     *
     * `sellerOffers()` orders by ex-works, which used to be the same ordering:
     * freight is per product and destination, so it shifted every offer equally.
     * A seller-quoted city does not — one seller can undercut another at that
     * destination while having the higher ex-works price.
     *
     * @param  array<int, array<string, mixed>>  $rows
     * @return array<int, array<string, mixed>>
     */
    private function cheapestFirst(array $rows): array
    {
        usort($rows, fn ($a, $b) => ($a['for_price'] ?? 0) <=> ($b['for_price'] ?? 0));

        return $rows;
    }
}
