<?php

namespace App\Http\Controllers\Api\V2\Seller;

use App\Http\Controllers\Controller;
use App\Models\SellerCommodityProduct;
use App\Models\SellerProductForPrice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * A seller's own F.O.R (doorstep) prices, one per destination city.
 *
 * Without a row here the F.O.R price is derived — ex-works plus the cheapest
 * transporter rate to that city. A row overrides it: for that city the buyer
 * sees exactly what the seller quoted, freight included. See
 * ProductPricingService::sellerForPrice().
 *
 * State and city come from the `addresses` lookup (/address/states and
 * /address/states/{state}/cities), which is already grouped so a city appears
 * once however many pincodes it has.
 */
class ProductForPriceController extends Controller
{
    /** Every destination this listing is quoted for, newest state/city order. */
    public function index(Request $request, int $id): JsonResponse
    {
        $product = $this->findOwned($request, $id);
        if ($product instanceof JsonResponse) {
            return $product;
        }

        return response()->json([
            'success' => true,
            'data'    => $this->rows($product),
        ]);
    }

    /**
     * Create or update one destination's price — deliberately one endpoint.
     *
     * The seller does not think of "add a city" and "change a city" as two
     * actions: they pick a state and city and type a number. The unique key on
     * (product, state, city) makes that an upsert, so re-quoting a city can
     * never leave a second row behind for the pricing lookup to choose from.
     */
    public function store(Request $request, int $id): JsonResponse
    {
        $product = $this->findOwned($request, $id);
        if ($product instanceof JsonResponse) {
            return $product;
        }

        if ($denied = $this->denyWithoutAccess($product)) {
            return $denied;
        }

        $data = $request->validate([
            'state' => ['required', 'string', 'max:120'],
            'city'  => ['required', 'string', 'max:120'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        $existing = SellerProductForPrice::where('product_id', $product->id)
            ->where('state', $data['state'])
            ->where('city', $data['city'])
            ->first();

        SellerProductForPrice::updateOrCreate(
            [
                'product_id' => $product->id,
                'state'      => $data['state'],
                'city'       => $data['city'],
            ],
            [
                'user_id' => $product->user_id,
                'price'   => $data['price'],
            ]
        );

        return response()->json([
            'success' => true,
            'message' => $existing
                ? "F.O.R price updated for {$data['city']}."
                : "F.O.R price added for {$data['city']}.",
            'data'    => $this->rows($product),
        ]);
    }

    /**
     * Drop one destination's price.
     *
     * Needed because the price is an override: leaving a mistyped city in place
     * would keep overriding the calculated F.O.R for that buyer indefinitely,
     * and there is no other way to get back to the derived number.
     */
    public function destroy(Request $request, int $id, int $priceId): JsonResponse
    {
        $product = $this->findOwned($request, $id);
        if ($product instanceof JsonResponse) {
            return $product;
        }

        if ($denied = $this->denyWithoutAccess($product)) {
            return $denied;
        }

        // Scoped to the product, so an id belonging to another listing — or
        // another seller — is a 404 rather than a delete.
        $row = SellerProductForPrice::where('product_id', $product->id)->find($priceId);

        if (! $row) {
            return response()->json(['success' => false, 'message' => 'F.O.R price not found.'], 404);
        }

        $city = $row->city;
        $row->delete();

        return response()->json([
            'success' => true,
            'message' => "F.O.R price removed for {$city}.",
            'data'    => $this->rows($product),
        ]);
    }

    /** @return array<int, array{id: int, state: string, city: string, price: string}> */
    private function rows(SellerCommodityProduct $product): array
    {
        return SellerProductForPrice::where('product_id', $product->id)
            ->orderBy('state')
            ->orderBy('city')
            ->get(['id', 'state', 'city', 'price'])
            ->map(fn ($row) => [
                'id'    => $row->id,
                'state' => $row->state,
                'city'  => $row->city,
                'price' => (string) $row->price,
            ])
            ->all();
    }

    /**
     * Admin grants this panel per seller (users.for_price_access), off by
     * default. The frontend hides the card on the same flag, but the check has
     * to live here too — hiding a card is not an authorisation control.
     *
     * F.O.B is a separate grant (users.fob_price_access) and is not consulted
     * here; these endpoints only ever write F.O.R prices.
     */
    private function denyWithoutAccess(SellerCommodityProduct $product): ?JsonResponse
    {
        if ($product->getUser?->for_price_access) {
            return null;
        }

        return response()->json([
            'success' => false,
            'message' => 'F.O.R price updates are not enabled for your account. Please contact Biznie support.',
        ], 403);
    }

    /** The seller's own product, or a 404. */
    private function findOwned(Request $request, int $id): SellerCommodityProduct|JsonResponse
    {
        $user = $request->user();
        $ownerId = $user->is_staff ? (int) $user->added_by : (int) $user->id;

        $product = SellerCommodityProduct::where('user_id', $ownerId)->find($id);

        if (! $product) {
            return response()->json(['success' => false, 'message' => 'Product not found.'], 404);
        }

        return $product;
    }
}
