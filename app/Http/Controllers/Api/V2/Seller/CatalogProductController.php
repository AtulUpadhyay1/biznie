<?php

namespace App\Http\Controllers\Api\V2\Seller;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\CommodityProduct;
use App\Models\CommodityProductState;
use App\Models\ProductUnit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * The admin catalog, as the Add Product wizard sees it.
 *
 * Typing a product name on step 1 searches `commodity_products` here. Picking a
 * suggestion links the seller's listing to that catalog product — the same link
 * the old /commodity-products screen made through its four cascading dropdowns —
 * and prefills the wizard from it, so a seller listing an already-catalogued
 * product only has to price it.
 *
 * The prefill is returned in the wizard's own form shape rather than as raw
 * columns: the client drops it straight into react-hook-form, and the mapping
 * from catalog columns to form fields stays on the side that owns both.
 */
class CatalogProductController extends Controller
{
    /** How many suggestions the type-ahead shows at once. */
    private const SUGGESTION_LIMIT = 10;

    /**
     * Name suggestions for the wizard's Product Name field.
     */
    public function index(Request $request): JsonResponse
    {
        $search = trim($request->string('search')->toString());

        // An unfiltered list is not a suggestion list — it would just be the
        // first ten products in the catalog, which tells the seller nothing.
        if (mb_strlen($search) < 2) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $list = CommodityProduct::active()
            ->search($search)
            ->when($request->integer('category_id'), fn ($q, $id) => $q->where('category_id', $id))
            ->with(['getCategory', 'getSubCategory', 'getUnit'])
            ->orderBy('name')
            ->limit(self::SUGGESTION_LIMIT)
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $list->map(fn (CommodityProduct $product) => [
                'id'           => $product->id,
                'name'         => $product->name,
                'thumbnail'    => $product->thumbnail ? imageUrl($product->thumbnail) : null,
                'category'     => $product->getCategory?->name,
                'sub_category' => $product->getSubCategory?->name,
                'unit'         => $product->getUnit?->short_name,
                'hsn_code'     => $product->hsn_code,
            ])->values(),
        ]);
    }

    /**
     * Everything the wizard needs once a suggestion is picked: the form values
     * to prefill, the images to carry over, and the brand / state / city combos
     * the catalog actually has prices for.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $product = CommodityProduct::active()
            ->with(['getCategory', 'getSubCategory', 'getUnit', 'getCommodityProductVariation'])
            ->find($id);

        if (! $product) {
            return response()->json([
                'success' => false,
                'message' => 'Catalog product not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'id'        => $product->id,
                'name'      => $product->name,
                'thumbnail' => $product->thumbnail ? imageUrl($product->thumbnail) : null,
                'images'    => $this->images($product),
                'brands'    => $this->brands($product),
                'locations' => $this->locations($product),
                'prefill'   => $this->prefill($product),
            ],
        ]);
    }

    /* --------------------------------------------------------------------- */
    /* Prefill                                                               */
    /* --------------------------------------------------------------------- */

    /**
     * Catalog columns mapped onto the wizard's form fields.
     *
     * Keys and value shapes match `ProductController::formData()`, so the edit
     * screen's mapper and this one feed the form the same thing.
     *
     * Left out on purpose: `product_type` and `short_description` (the catalog
     * has no equivalent), and the specification rows (the admin catalog form
     * stopped collecting them, so they are empty on anything recent).
     *
     * @return array<string, mixed>
     */
    private function prefill(CommodityProduct $product): array
    {
        $unit = $product->getUnit?->short_name ?? '';

        return [
            'product_name'         => (string) $product->name,
            'category'             => $product->category_id ? (string) $product->category_id : '',
            'sub_category'         => $product->sub_category_id ? (string) $product->sub_category_id : '',
            'detailed_description' => (string) ($product->description ?? ''),
            'hsn_code'             => (string) ($product->hsn_code ?? ''),
            'tax_rate'             => $this->number($product->gst),
            'product_video'        => (string) ($product->video_url ?? ''),

            'qualities'            => $this->qualities($product),
            'quality_charge'       => $this->number($product->quality_charge),

            'packaging'            => $this->packaging($product),

            'weight_unit'          => $unit,

            'variants'             => $this->variants($product),

            'moq'                  => $product->min_order_qty ? $this->number($product->min_order_qty) : '',
            'moq_unit'             => $unit,

            'charges'              => $this->charges($product),
        ];
    }

    /**
     * The grades the catalog offers, all ticked.
     *
     * A catalog-linked product may not invent grades — `ProductController`
     * treats the parent's list as the menu — so the seller starts from the full
     * list and unticks what they do not carry.
     *
     * @return array<int, array{name: string, price: string, is_selected: bool}>
     */
    private function qualities(CommodityProduct $product): array
    {
        $names = is_array($product->quality) ? array_values($product->quality) : [];
        $prices = is_array($product->quality_price) ? array_values($product->quality_price) : [];

        $rows = [];
        foreach ($names as $i => $name) {
            if (trim((string) $name) === '') {
                continue;
            }
            $rows[] = [
                'name'        => (string) $name,
                'price'       => (string) ($prices[$i] ?? ''),
                'is_selected' => true,
            ];
        }

        return $rows;
    }

    /**
     * Packaging types the catalog lists, with the catalog's charge as a start.
     *
     * `packaging_type_price` is keyed by packaging type id — the same shape
     * ProductPricingService reads back — so the charge is looked up by id, not
     * by position.
     *
     * @return array<int, array{type: string, charge: string}>
     */
    private function packaging(CommodityProduct $product): array
    {
        $types = is_array($product->packaging_type) ? $product->packaging_type : [];
        $prices = is_array($product->packaging_type_price) ? $product->packaging_type_price : [];

        $rows = [];
        foreach ($types as $type) {
            $typeId = (int) $type;
            if ($typeId <= 0) {
                continue;
            }
            $rows[] = [
                'type'   => (string) $typeId,
                'charge' => $this->number($prices[$typeId] ?? null),
            ];
        }

        return $rows;
    }

    /**
     * The catalog's variations as free-form wizard rows ("8", unit "MM").
     *
     * Not as catalog rows with an id: those are `seller_commodity_product_state_prices`,
     * which only exist once a brand, state and city are known — and the wizard
     * does not ask for those until steps 3 and 6. The seller prices these rows
     * like their own, and they save to the product's `variation` column.
     *
     * @return array<int, array<string, string|bool>>
     */
    private function variants(CommodityProduct $product): array
    {
        $unitNames = $this->unitShortNames($product);

        $rows = [];
        $seen = [];

        foreach ($product->getCommodityProductVariation as $variation) {
            $values = is_array($variation->value) ? $variation->value : [];

            // One variation can carry several attributes (Size 8, Grade A). The
            // form has a single size/unit pair per row, so multi-attribute
            // values are joined into the label the rest of the platform shows.
            $sizeParts = [];
            $unit = '';
            foreach ($values as $value) {
                $name = (string) ($value['name'] ?? '');
                $sizeParts[] = trim((string) ($value['value'] ?? ''));
                $unit = $unit !== '' ? $unit : ($unitNames[$name] ?? '');
            }

            $size = trim(implode(' · ', array_filter($sizeParts)));
            if ($size === '' || in_array($size, $seen, true)) {
                continue;
            }
            $seen[] = $size;

            $rows[] = [
                'id'          => '',
                'label'       => trim($size.' '.$unit),
                'size'        => $size,
                'unit'        => $unit,
                'charge'      => '',
                'stock'       => '',
                'is_selected' => true,
            ];
        }

        return $rows;
    }

    /**
     * Unit short names keyed by attribute name ("Size" => "MM").
     *
     * The catalog's `unit` column maps each attribute to a product_units id.
     *
     * @return array<string, string>
     */
    private function unitShortNames(CommodityProduct $product): array
    {
        $map = is_array($product->unit) ? $product->unit : [];
        if (! $map) {
            return [];
        }

        $names = ProductUnit::whereIn('id', array_values($map))->pluck('short_name', 'id');

        return collect($map)->map(fn ($unitId) => (string) ($names[$unitId] ?? ''))->all();
    }

    /**
     * The catalog's charges in the wizard's row shape.
     *
     * The four with a column of their own come first (the order the form lists
     * them in), then any extras the catalog added, each with its operator.
     *
     * @return array<int, array{type: string, enabled: bool, amount: string, operator: string}>
     */
    private function charges(CommodityProduct $product): array
    {
        $known = [
            'Loading'   => $product->loading_charge,
            'Insurance' => $product->insurance_charge,
            'GST'       => $product->gst,
            'TCS'       => $product->tcs,
        ];

        $rows = [];
        foreach ($known as $type => $value) {
            $amount = (float) $value;
            $rows[] = [
                'type'     => $type,
                'enabled'  => $amount > 0,
                'amount'   => $amount > 0 ? $this->number($amount) : '',
                'operator' => '+',
            ];
        }

        $names = is_array($product->charge_name) ? array_values($product->charge_name) : [];
        $prices = is_array($product->charge_price) ? array_values($product->charge_price) : [];
        $operators = is_array($product->operator) ? array_values($product->operator) : [];

        foreach ($names as $i => $name) {
            if (trim((string) $name) === '') {
                continue;
            }
            $rows[] = [
                'type'     => (string) $name,
                'enabled'  => true,
                'amount'   => $this->number($prices[$i] ?? null),
                'operator' => in_array($operators[$i] ?? '+', ['+', '-'], true) ? $operators[$i] : '+',
            ];
        }

        return $rows;
    }

    /* --------------------------------------------------------------------- */
    /* Catalog reach: which brands, and where                                */
    /* --------------------------------------------------------------------- */

    /**
     * The brands this product is actually sold under.
     *
     * Taken from `commodity_product_states` rather than the catalog's own
     * `brand_id` list, so the seller can only pick a brand the catalog has a
     * state and a price for — the same restriction the old screen's cascading
     * dropdowns enforced.
     *
     * @return array<int, array{id: int, name: string}>
     */
    private function brands(CommodityProduct $product): array
    {
        $brandIds = CommodityProductState::where('commodity_product_id', $product->id)
            ->pluck('brand_id')
            ->unique()
            ->filter()
            ->values();

        if ($brandIds->isEmpty()) {
            return [];
        }

        return Brand::whereIn('id', $brandIds)
            ->where('status', '1')
            ->orderBy('name')
            ->get()
            ->map(fn (Brand $brand) => ['id' => (int) $brand->id, 'name' => (string) $brand->name])
            ->values()
            ->all();
    }

    /**
     * Brand / state / city combos the catalog stocks, for the loading dropdowns.
     *
     * Flat rather than nested per brand: the list is one product's footprint,
     * small enough that the client filters it in place instead of asking again
     * every time the brand changes.
     *
     * @return array<int, array{brand_id: int, state: string, cities: array<int, string>}>
     */
    private function locations(CommodityProduct $product): array
    {
        return CommodityProductState::where('commodity_product_id', $product->id)
            ->get(['brand_id', 'state', 'city'])
            ->filter(fn ($row) => $row->brand_id && trim((string) $row->state) !== '')
            ->groupBy(fn ($row) => $row->brand_id.'|'.$row->state)
            ->map(fn ($rows) => [
                'brand_id' => (int) $rows->first()->brand_id,
                'state'    => (string) $rows->first()->state,
                'cities'   => $rows->pluck('city')
                    ->filter(fn ($city) => trim((string) $city) !== '')
                    ->unique()
                    ->sort()
                    ->values()
                    ->all(),
            ])
            ->sortBy('state')
            ->values()
            ->all();
    }

    /* --------------------------------------------------------------------- */
    /* Small helpers                                                         */
    /* --------------------------------------------------------------------- */

    /** @return array<int, array{id: int, url: string}> */
    private function images(CommodityProduct $product): array
    {
        return collect(is_array($product->images) ? $product->images : [])
            ->map(fn ($imageId) => ['id' => (int) $imageId, 'url' => imageUrl($imageId)])
            ->filter(fn ($image) => $image['url'] !== '')
            ->values()
            ->all();
    }

    /** Numbers as the form wants them: a plain string, blank when unset or zero. */
    private function number(mixed $value): string
    {
        if ($value === null || $value === '' || (float) $value == 0.0) {
            return '';
        }

        return rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.');
    }
}
