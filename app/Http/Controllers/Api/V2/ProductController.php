<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\ProductDetailResource;
use App\Http\Resources\V2\ProductOfferResource;
use App\Http\Resources\V2\ProductResource;
use App\Models\CommodityProduct;
use App\Services\ProductPricingService;
use App\Support\ProductSearch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $q = CommodityProduct::active()
            ->with(['getCategory', 'getSubCategory', 'getUnit']);

        // Before latest(): the search adds a relevance ordering that must win.
        $parsed = null;
        if ($search = trim($request->string('search')->toString())) {
            $parsed = ProductSearch::parse($search);
            $parsed->apply($q);
        }
        $q->latest();
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
                // What the query was read as, e.g. "saria 12mm" -> "TMT · 12mm".
                'search'       => $parsed ? [
                    'query'           => $parsed->raw,
                    'interpreted'     => $parsed->interpreted(),
                    'synonym_applied' => $parsed->synonymApplied,
                ] : null,
            ],
        ]);
    }

    /**
     * Header autocomplete (B05): from the second letter, the last word read
     * as unfinished — "sar" already offers TMT, "pat" offers Flat.
     */
    public function suggest(Request $request): JsonResponse
    {
        $term = trim($request->string('q')->toString());
        if (mb_strlen($term) < 2) {
            return response()->json(['success' => true, 'data' => [], 'meta' => ['interpreted' => null]]);
        }

        $parsed = ProductSearch::parse($term, prefix: true);
        $list = $parsed->apply(CommodityProduct::active()->with('getCategory'))
            ->orderBy('name')
            ->limit(8)
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $list->map(fn (CommodityProduct $p) => [
                'id'       => $p->id,
                'name'     => $p->name,
                'slug'     => $p->slug,
                'category' => $p->getCategory?->name,
            ])->values(),
            'meta'    => ['interpreted' => $parsed->synonymApplied ? $parsed->interpreted() : null],
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
        // Ex-Works mode has no F.O.R; rank on what the card shows.
        usort($rows, fn ($a, $b) => ($a['for_price'] ?? $a['ex_works_price'] ?? 0) <=> ($b['for_price'] ?? $b['ex_works_price'] ?? 0));

        return $rows;
    }
}
