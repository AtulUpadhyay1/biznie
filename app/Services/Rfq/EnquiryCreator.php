<?php

namespace App\Services\Rfq;

use App\Models\CommodityProduct;
use App\Models\ProductEnquiry;
use App\Models\ProductUnit;
use App\Models\User;
use Carbon\Carbon;

/**
 * Raises an RFQ and immediately opens bidding on it.
 *
 * Shared by `POST /me/enquiries` and the chat assistant so both entry points
 * produce exactly the same row, reference, timeline and seller fan-out.
 *
 * Quantity, unit, size and delivery city are real columns (they used to be
 * squashed into `description` as free text, which left nothing to price
 * freight against). The RFQ is fanned out to matching sellers in the same
 * request rather than waiting for an admin to hand-pick them.
 *
 * Dispatch failures are swallowed on purpose: the buyer's RFQ is saved either
 * way, and the admin screen can re-notify.
 */
class EnquiryCreator
{
    public function __construct(private readonly LiveBiddingService $bidding)
    {
    }

    /**
     * @param  array<string, mixed>  $data  Already-validated input.
     * @param  bool  $fanOut  false when the caller is inside a transaction:
     *                        it must then call fanOut() itself once that has
     *                        committed, so no seller is pushed an RFQ number a
     *                        rollback could still take back (and reuse).
     */
    public function create(User $user, array $data, bool $fanOut = true): ProductEnquiry
    {
        $enquiry = new ProductEnquiry();
        $enquiry->user_id              = $user->is_staff ? $user->added_by : $user->id;
        $enquiry->commodity_product_id = $data['commodity_product_id'];
        $enquiry->brand_id             = $data['brand_id'] ?? null;
        $enquiry->unique_id            = $this->nextUniqueId();
        $enquiry->origin_city          = $data['origin_city'] ?? null;
        $enquiry->variation            = $data['variation'] ?? [];
        $enquiry->billing_address      = $data['billing_address'] ?? null;
        $enquiry->delivery_address     = $data['delivery_address'] ?? null;
        $enquiry->consignee_detail     = $data['consignee_detail'] ?? null;
        $enquiry->quality              = $data['quality'] ?? null;
        $enquiry->packaging_charge     = $data['packaging_charge'] ?? null;
        $enquiry->purpose              = $data['purpose'] ?? null;
        $enquiry->description          = $data['description'] ?? null;
        $enquiry->price                = $data['price'] ?? null;
        $enquiry->payment_mode         = $data['payment_mode'] ?? null;
        $enquiry->credit_day           = $data['credit_day'] ?? null;
        // The single quantity, or the sum of the size rows: without it a
        // size-wise RFQ showed "Quantity —" to sellers and the buyer's live
        // panel had no total amount.
        $enquiry->quantity             = self::totalQuantity($data) ?: null;
        $enquiry->unit_id              = $data['unit_id'] ?? null;
        $enquiry->unit_label           = $this->unitLabel($data);
        $enquiry->size_label           = $data['size_label'] ?? null;
        $enquiry->price_option         = $data['price_option'] ?? null;
        $enquiry->required_by          = $data['required_by'] ?? null;
        $enquiry->status               = 'pending';
        $enquiry->history              = [['status' => 'pending', 'created_at' => Carbon::now()->toIso8601String()]];

        // Sellers are only ever shown the city, so it is resolved once here
        // rather than dug out of the address blob on every read.
        [$city, $state] = $this->resolveDeliveryPlace($data);
        $enquiry->delivery_city  = $city;
        $enquiry->delivery_state = $state;

        $enquiry->save();

        if ($fanOut) {
            $this->fanOut($enquiry);
        }

        return $enquiry;
    }

    /**
     * The RFQ's total quantity: its single `quantity` (Rate Finder), else the
     * sum of the per-size rows (variant builder).
     */
    public static function totalQuantity(array $data): float
    {
        if (isset($data['quantity']) && is_numeric($data['quantity']) && (float) $data['quantity'] > 0) {
            return (float) $data['quantity'];
        }

        $total = 0.0;
        foreach (is_array($data['variation'] ?? null) ? $data['variation'] : [] as $row) {
            if (is_array($row) && is_numeric($row['quantity'] ?? null)) {
                $total += (float) $row['quantity'];
            }
        }

        return round($total, 2);
    }

    /**
     * Why this RFQ is below the product's minimum order, or null when it is
     * not (B14). Shared by the API and the chat assistant so neither can raise
     * an RFQ a seller would have to refuse.
     */
    public static function minimumOrderError(array $data): ?string
    {
        $product = CommodityProduct::with('getUnit')->find($data['commodity_product_id'] ?? null);
        $moq = (float) ($product?->min_order_qty ?? 0);
        $total = self::totalQuantity($data);

        if ($total <= 0) {
            return 'Enter the quantity you need';
        }

        if ($moq > 0 && $total < $moq) {
            $unit = $data['unit_label'] ?? $product?->getUnit?->short_name ?? 'MT';
            // "MT", never "Mt" (4.12).
            $unit = in_array(strtolower(trim($unit)), ['mt', 'metric ton', 'metric tonne'], true) ? 'MT' : $unit;
            $fmt = fn (float $n) => rtrim(rtrim(number_format($n, 2, '.', ''), '0'), '.');

            return "Minimum order is {$fmt($moq)} {$unit} for this product (you entered {$fmt($total)} {$unit})";
        }

        return null;
    }

    /** Opens bidding and invites (and pushes) matching sellers. Never throws. */
    public function fanOut(ProductEnquiry $enquiry): void
    {
        try {
            $this->bidding->openBidding($enquiry);
            $this->bidding->dispatchToSellers($enquiry);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /**
     * Collision-resistant RFQ reference.
     *
     * The old `rand(1111, 9999)` suffix gave a real collision chance per day,
     * and `unique_id` is what the seller app, the bid rows and every
     * notification key on - two RFQs sharing one is not a cosmetic problem. A
     * daily sequence cannot repeat.
     */
    private function nextUniqueId(): string
    {
        $prefix = 'BZN-RFQ-' . date('ymd') . '-';

        $last = ProductEnquiry::withTrashed()
            ->where('unique_id', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('unique_id');

        $next = $last ? ((int) substr($last, strlen($prefix))) + 1 : 1;

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    /** Falls back to the unit's short name when the client sent no label. */
    private function unitLabel(array $data): ?string
    {
        if (! empty($data['unit_label'])) {
            return $data['unit_label'];
        }

        if (! empty($data['unit_id'])) {
            return ProductUnit::whereKey($data['unit_id'])->value('short_name');
        }

        return null;
    }

    /**
     * Where the load has to land, as a (city, state) pair.
     *
     * An explicit `delivery_city` from the Rate Finder wins; otherwise it comes
     * out of whichever address the detailed form filled in, so both entry
     * points end up with a city that freight can be priced against.
     */
    private function resolveDeliveryPlace(array $data): array
    {
        if (! empty($data['delivery_city'])) {
            return [$data['delivery_city'], $data['delivery_state'] ?? null];
        }

        foreach (['delivery_address', 'consignee_detail', 'billing_address'] as $key) {
            $address = $data[$key] ?? null;
            if (is_array($address)) {
                $address = array_is_list($address) ? ($address[0] ?? null) : $address;
            }
            if (is_array($address) && ! empty($address['city'])) {
                return [$address['city'], $address['state'] ?? null];
            }
        }

        return [null, null];
    }
}
