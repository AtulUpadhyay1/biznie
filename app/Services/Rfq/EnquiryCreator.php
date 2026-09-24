<?php

namespace App\Services\Rfq;

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
        $enquiry->quantity             = $data['quantity'] ?? null;
        $enquiry->unit_id              = $data['unit_id'] ?? null;
        $enquiry->unit_label           = $this->unitLabel($data);
        $enquiry->size_label           = $data['size_label'] ?? null;
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
