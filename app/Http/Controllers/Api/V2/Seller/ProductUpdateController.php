<?php

namespace App\Http\Controllers\Api\V2\Seller;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\Seller\SellerProductResource;
use App\Models\BookmarkProduct;
use App\Models\HomeProduct;
use App\Models\ProductUnit;
use App\Models\SellerCommodityProduct;
use App\Models\SellerCommodityProductHistory;
use App\Models\SellerCommodityProductStatePrice;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * The seller's day-to-day edits on an existing product: price, variation prices,
 * quality prices and stock.
 *
 * These are deliberately separate from ProductController's wizard. The wizard
 * describes the product and is locked once it goes for review; these panels are
 * operating data a seller changes repeatedly on a live listing, so they stay
 * open for anything that is not currently sitting with a reviewer.
 *
 * Variation, quality and stock all work off the catalog the product was created
 * from: the seller picks which of the catalog's variations and grades they sell
 * and what they charge over base price for each. The rows live in
 * `seller_commodity_product_state_prices`; the grade list comes from the parent
 * CommodityProduct.
 */
class ProductUpdateController extends Controller
{
    /**
     * Everything the variation, quality and stock panels need, in one request —
     * variation and stock are two views of the same rows, so splitting them into
     * separate endpoints would just fetch the same table twice.
     */
    public function attributes(Request $request, int $id): JsonResponse
    {
        $product = $this->findOwned($request, $id);
        if ($product instanceof JsonResponse) {
            return $product;
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'variants'  => $this->variantRows($product),
                'qualities' => $this->qualityRows($product),
            ],
        ]);
    }

    /**
     * The four fields a seller changes together whenever the market moves: the
     * price itself, how fast it can be dispatched, how much is on offer and how
     * long the quote stands. Splitting them into separate saves was the old
     * app's shape and meant a price could go live with a stale validity.
     */
    public function updatePrice(Request $request, int $id): JsonResponse
    {
        $product = $this->findEditable($request, $id);
        if ($product instanceof JsonResponse) {
            return $product;
        }

        $data = $request->validate([
            'base_price'     => ['required', 'numeric', 'min:0'],
            'load_within'    => ['required', 'integer', 'min:0', 'max:365'],
            'quantity'       => ['required', 'numeric', 'min:0'],
            'price_validity' => ['required', 'date'],
        ], [], ['load_within' => 'dispatch within (days)']);

        DB::transaction(function () use ($product, $data) {
            $product->base_price = $data['base_price'];
            $product->load_within = $data['load_within'];
            $product->quantity = $data['quantity'];
            // Kept in the legacy string format: the admin price reports sort and
            // filter on STR_TO_DATE(price_validity, '%Y-%m-%d %h:%i %p').
            $product->price_validity = Carbon::parse($data['price_validity'])->format('Y-m-d h:i A');
            $product->save();

            $this->recordHistory($product);
            $this->syncHomeProduct($product);
        });

        $this->notifyWatchers($product);

        return $this->ok($product, 'Product price updated successfully.');
    }

    /**
     * Which catalog variations this seller offers, and the gauge difference
     * charged on each over the base price.
     */
    public function updateVariants(Request $request, int $id): JsonResponse
    {
        $product = $this->findEditable($request, $id);
        if ($product instanceof JsonResponse) {
            return $product;
        }

        $validator = Validator::make($request->all(), [
            'variants'               => ['required', 'array', 'min:1'],
            'variants.*.id'          => ['required', 'integer'],
            'variants.*.is_selected' => ['required', 'boolean'],
            'variants.*.price'       => ['nullable', 'numeric', 'min:0'],
        ], [], ['variants.*.price' => 'price']);

        // A variation the seller offers has to carry a price; one they do not
        // offer may leave it blank. `required_if` cannot express that per row.
        $validator->after(function ($v) use ($request) {
            foreach ((array) $request->input('variants', []) as $i => $row) {
                if (filter_var($row['is_selected'] ?? false, FILTER_VALIDATE_BOOLEAN)
                    && trim((string) ($row['price'] ?? '')) === '') {
                    $v->errors()->add("variants.{$i}.price", 'Enter a price for the selected variation.');
                }
            }
        });

        $data = $validator->validate();

        $rows = $this->ownedStatePrices($product, array_column($data['variants'], 'id'));
        if ($rows instanceof JsonResponse) {
            return $rows;
        }

        DB::transaction(function () use ($product, $data, $rows) {
            foreach ($data['variants'] as $row) {
                $model = $rows[$row['id']];
                $model->is_selected = filter_var($row['is_selected'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
                $model->price = trim((string) ($row['price'] ?? '')) === '' ? '0' : (string) $row['price'];
                $model->save();
            }

            $this->recordHistory($product);
        });

        return $this->ok($product, 'Variation price updated successfully.');
    }

    /**
     * Which grades from the parent catalog this seller offers, and the price
     * difference charged on each.
     */
    public function updateQuality(Request $request, int $id): JsonResponse
    {
        $product = $this->findEditable($request, $id);
        if ($product instanceof JsonResponse) {
            return $product;
        }

        $validator = Validator::make($request->all(), [
            'qualities'               => ['required', 'array', 'min:1'],
            'qualities.*.name'        => ['required', 'string', 'max:190'],
            'qualities.*.is_selected' => ['required', 'boolean'],
            'qualities.*.price'       => ['nullable', 'numeric', 'min:0'],
        ], [], ['qualities.*.price' => 'price']);

        $validator->after(function ($v) use ($request) {
            foreach ((array) $request->input('qualities', []) as $i => $row) {
                if (filter_var($row['is_selected'] ?? false, FILTER_VALIDATE_BOOLEAN)
                    && trim((string) ($row['price'] ?? '')) === '') {
                    $v->errors()->add("qualities.{$i}.price", 'Enter a price for the selected quality.');
                }
            }
        });

        $data = $validator->validate();

        // Only the offered grades are stored, and `quality_price` is positional
        // against `quality` — the two arrays must be built together.
        $selected = collect($data['qualities'])
            ->filter(fn ($row) => filter_var($row['is_selected'], FILTER_VALIDATE_BOOLEAN))
            ->values();

        DB::transaction(function () use ($product, $selected) {
            $product->quality = $selected->pluck('name')->all();
            $product->quality_price = $selected
                ->map(fn ($row) => trim((string) ($row['price'] ?? '')) === '' ? '0' : (string) $row['price'])
                ->all();
            $product->save();

            $this->recordHistory($product);
        });

        return $this->ok($product, 'Product quality updated successfully.');
    }

    /**
     * Stock held against each variation the seller offers.
     *
     * Which rows are editable is decided here, from `is_selected` in the
     * database, rather than taken from the request — a seller cannot put stock
     * against a variation they do not sell.
     */
    public function updateStock(Request $request, int $id): JsonResponse
    {
        $product = $this->findEditable($request, $id);
        if ($product instanceof JsonResponse) {
            return $product;
        }

        $data = $request->validate([
            'stock'         => ['required', 'array', 'min:1'],
            'stock.*.id'    => ['required', 'integer'],
            'stock.*.stock' => ['nullable', 'string', 'max:60'],
        ], [], ['stock.*.stock' => 'stock']);

        $rows = $this->ownedStatePrices($product, array_column($data['stock'], 'id'));
        if ($rows instanceof JsonResponse) {
            return $rows;
        }

        $errors = [];
        foreach ($data['stock'] as $i => $row) {
            if ((int) $rows[$row['id']]->is_selected === 1 && trim((string) ($row['stock'] ?? '')) === '') {
                $errors["stock.{$i}.stock"] = ['Enter the stock for the selected variation.'];
            }
        }
        if ($errors) {
            return response()->json([
                'success' => false,
                'message' => 'The selected stock cannot be blank.',
                'errors'  => $errors,
            ], 422);
        }

        DB::transaction(function () use ($product, $data, $rows) {
            foreach ($data['stock'] as $row) {
                $model = $rows[$row['id']];
                if ((int) $model->is_selected !== 1) {
                    continue;
                }
                $model->stock = (string) $row['stock'];
                $model->save();
            }

            $this->recordHistory($product);
        });

        return $this->ok($product, 'Variation stock updated successfully.');
    }

    /**
     * Which of the three prices this listing shows buyers.
     *
     * `findOwned` rather than `findEditable`: this changes nothing a reviewer is
     * assessing, so a seller can still take a price off their storefront while
     * the product sits in review.
     *
     * Limited to the sellers in config/biznie.php while the feature is piloted;
     * everyone else keeps whatever their listings are already set to. The panel
     * is hidden for them too, but the check has to live here — hiding a card is
     * not an authorisation control.
     *
     * Independent of `users.for_price_access` / `fob_price_access`. Those grants
     * govern whether the seller may quote their own freight; the F.O.R price
     * itself is derived from transporter rates for every listing, so gating its
     * display on the grant would blank the storefront for sellers who never
     * needed to quote a city.
     */
    public function updatePriceVisibility(Request $request, int $id): JsonResponse
    {
        $product = $this->findOwned($request, $id);
        if ($product instanceof JsonResponse) {
            return $product;
        }

        if (! in_array((int) $product->user_id, config('biznie.price_visibility_user_ids', []), true)) {
            return response()->json([
                'success' => false,
                'message' => 'Price visibility is not enabled for your account.',
            ], 403);
        }

        $data = $request->validate([
            'show_ex_price'  => ['required', 'boolean'],
            'show_for_price' => ['required', 'boolean'],
            'show_fob_price' => ['required', 'boolean'],
        ]);

        $product->show_ex_price  = (bool) $data['show_ex_price'];
        $product->show_for_price = (bool) $data['show_for_price'];
        $product->show_fob_price = (bool) $data['show_fob_price'];
        $product->save();

        return $this->ok($product, 'Price visibility updated successfully.');
    }

    /* --------------------------------------------------------------------- */
    /* Panel payloads                                                        */
    /* --------------------------------------------------------------------- */

    /**
     * One row per catalog variation the product was created with.
     *
     * @return array<int, array<string, mixed>>
     */
    private function variantRows(SellerCommodityProduct $product): array
    {
        $units = $this->unitShortNames($product);

        return $product->getStatePrice
            ->map(function (SellerCommodityProductStatePrice $row) use ($units) {
                $values = is_array($row->value) ? $row->value : [];

                $attributes = [];
                foreach ($values as $value) {
                    $name = (string) ($value['name'] ?? '');
                    $attributes[] = [
                        'name'  => $name,
                        'value' => (string) ($value['value'] ?? ''),
                        'unit'  => $units[$name] ?? '',
                    ];
                }

                return [
                    'id'          => $row->id,
                    // "8 MM" — what the old app showed under "Requirements".
                    'label'       => collect($attributes)
                        ->map(fn ($a) => trim($a['value'].' '.$a['unit']))
                        ->filter()
                        ->implode(' · '),
                    'attributes'  => $attributes,
                    'price'       => $row->price !== null ? (string) $row->price : '',
                    'stock'       => $row->stock !== null ? (string) $row->stock : '',
                    'is_selected' => (int) $row->is_selected === 1,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Every grade the parent catalog offers, flagged with whether this seller
     * sells it, and priced at the seller's own figure where they do.
     *
     * @return array<int, array<string, mixed>>
     */
    private function qualityRows(SellerCommodityProduct $product): array
    {
        $commodity = $product->getCommodityProduct;

        $catalog = is_array($commodity?->quality) ? $commodity->quality : [];
        $catalogPrices = is_array($commodity?->quality_price) ? $commodity->quality_price : [];
        $chosen = is_array($product->quality) ? array_values($product->quality) : [];
        $chosenPrices = is_array($product->quality_price) ? array_values($product->quality_price) : [];

        $rows = [];
        foreach ($catalog as $i => $name) {
            $position = array_search($name, $chosen, true);
            $isSelected = $position !== false;

            $price = $catalogPrices[$i] ?? '0';
            if ($isSelected && isset($chosenPrices[$position])) {
                $price = $chosenPrices[$position];
            }

            $rows[] = [
                'name'          => (string) $name,
                'price'         => (string) $price,
                'catalog_price' => (string) ($catalogPrices[$i] ?? '0'),
                'is_selected'   => $isSelected,
            ];
        }

        return $rows;
    }

    /**
     * Attribute name ("Size", "Weight") to the short unit the catalog measures
     * it in ("MM", "Kg"), resolved in one query instead of per row.
     *
     * @return array<string, string>
     */
    private function unitShortNames(SellerCommodityProduct $product): array
    {
        $commodity = $product->getCommodityProduct;
        if (! $commodity || ! is_array($commodity->unit)) {
            return [];
        }

        $shortNames = ProductUnit::whereIn('id', array_filter(array_values($commodity->unit)))
            ->pluck('short_name', 'id');

        $map = [];
        foreach ($commodity->unit as $attribute => $unitId) {
            $map[$attribute] = (string) ($shortNames[$unitId] ?? '');
        }

        return $map;
    }

    /* --------------------------------------------------------------------- */
    /* Helpers                                                               */
    /* --------------------------------------------------------------------- */

    private function ownerId(Request $request): int
    {
        $user = $request->user();

        return $user->is_staff ? (int) $user->added_by : (int) $user->id;
    }

    /** The seller's own product, or a 404. */
    private function findOwned(Request $request, int $id): SellerCommodityProduct|JsonResponse
    {
        $product = SellerCommodityProduct::where('user_id', $this->ownerId($request))
            ->with(['getCommodityProduct', 'getStatePrice'])
            ->find($id);

        if (! $product) {
            return response()->json(['success' => false, 'message' => 'Product not found.'], 404);
        }

        return $product;
    }

    /** As `findOwned`, but also refuses products a reviewer is holding. */
    private function findEditable(Request $request, int $id): SellerCommodityProduct|JsonResponse
    {
        $product = $this->findOwned($request, $id);
        if ($product instanceof JsonResponse) {
            return $product;
        }

        if ($product->request_status === 'pending_review') {
            return response()->json([
                'success' => false,
                'message' => 'This product is locked while it is under review.',
            ], 422);
        }

        return $product;
    }

    /**
     * Loads the posted variation rows, scoped to this product.
     *
     * Scoping matters: the ids arrive from the browser, and looking them up
     * globally would let one seller write to another seller's variations.
     *
     * @param  array<int, int>  $ids
     * @return \Illuminate\Support\Collection<int, SellerCommodityProductStatePrice>|JsonResponse
     */
    private function ownedStatePrices(SellerCommodityProduct $product, array $ids)
    {
        $rows = SellerCommodityProductStatePrice::where('seller_commodity_product_id', $product->id)
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        if ($rows->count() !== count(array_unique($ids))) {
            return response()->json([
                'success' => false,
                'message' => 'These variations no longer match the product. Please reload and try again.',
            ], 422);
        }

        return $rows;
    }

    private function ok(SellerCommodityProduct $product, string $message): JsonResponse
    {
        $fresh = $product->fresh(['getCommodityProduct', 'getBrand', 'getCategory', 'getUnit', 'getStatePrice']);

        return response()->json([
            'success'    => true,
            'message'    => $message,
            'data'       => new SellerProductResource($fresh),
            // The panels reseed from this, so a save and its refreshed rows are
            // one round trip rather than two.
            'attributes' => [
                'variants'  => $this->variantRows($fresh),
                'qualities' => $this->qualityRows($fresh),
            ],
        ]);
    }

    /** Snapshot of the product after the edit, for the seller price history. */
    private function recordHistory(SellerCommodityProduct $product): void
    {
        $history = new SellerCommodityProductHistory;
        $history->user_id = $product->user_id;
        $history->commodity_product_id = $product->commodity_product_id;
        $history->seller_commodity_product_id = $product->id;
        $history->seller_commodity_product_detail = $product->toArray();
        $history->save();
    }

    /**
     * Keeps the "best price in this city" row the home page reads in step with
     * the seller's price.
     *
     * Only catalog-linked products have a home row — products created through the
     * v2 wizard have no `commodity_product_id`, so there is nothing to compare
     * them against and the sync is skipped.
     */
    private function syncHomeProduct(SellerCommodityProduct $product): void
    {
        if (! $product->commodity_product_id) {
            return;
        }

        $brandId = is_array($product->brand_id) ? ($product->brand_id[0] ?? null) : $product->brand_id;

        foreach ($this->loadingCities($product) as $city) {
            $home = HomeProduct::where('commodity_product_id', $product->commodity_product_id)
                ->where('brand_id', $brandId)
                ->where('city', $city)
                ->first();

            if ($home) {
                // Only take over the slot when this seller is now the cheapest.
                if ((float) $home->base_price > 0 && (float) $home->base_price > (float) $product->base_price) {
                    $home->user_id = $product->user_id;
                    $home->seller_commodity_product_id = $product->id;
                    $home->base_price = $product->base_price ?? 0;
                    $home->save();
                }
                continue;
            }

            $home = new HomeProduct;
            $home->user_id = $product->user_id;
            $home->commodity_product_id = $product->commodity_product_id;
            $home->seller_commodity_product_id = $product->id;
            $home->brand_id = $brandId;
            $home->city = $city;
            $home->base_price = $product->base_price ?? 0;
            $home->save();
        }
    }

    /**
     * `loading_address` is a list of addresses on catalog products but a single
     * `{city, state, country}` map on wizard products — normalise both to cities.
     *
     * @return array<int, string>
     */
    private function loadingCities(SellerCommodityProduct $product): array
    {
        $address = $product->loading_address;
        if (! is_array($address) || $address === []) {
            return array_filter([$product->city]);
        }

        $rows = array_is_list($address) ? $address : [$address];

        return collect($rows)
            ->map(fn ($row) => is_array($row) ? ($row['city'] ?? null) : null)
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /** Tells everyone who bookmarked this product that the price moved. */
    private function notifyWatchers(SellerCommodityProduct $product): void
    {
        if (! $product->commodity_product_id) {
            return;
        }

        $userIds = BookmarkProduct::where('commodity_product_id', $product->commodity_product_id)
            ->pluck('user_id')
            ->unique();

        foreach (User::whereIn('id', $userIds)->get() as $user) {
            sendNotification(
                $user,
                'Price Update Alert',
                'The price for the product '.$product->name.' has been updated. Check out the new price now!',
                'notification',
                [],
                true
            );
        }
    }
}
