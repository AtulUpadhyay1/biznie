<?php

namespace App\Services\Chat\Presenters;

use App\Models\ProductEnquiry;
use App\Services\Chat\ChatContext;
use App\Services\Rfq\LiveBiddingService;

/** ProductEnquiry → ChatEnquirySummary / ChatEnquiryDetail (plan §4.3). */
class EnquiryPresenter
{
    public const RELATIONS = [
        'getCommodityProduct:id,name,slug,thumbnail',
        'getCommodityProductOrder:id,product_enquiries_id,order_id',
    ];

    public function __construct(private readonly LiveBiddingService $bidding)
    {
    }

    /** Pre-bidding statuses where a seller has priced the RFQ. */
    private const LEGACY_REPLIED = ['seller replied', 'transporter replied'];

    /** Pre-bidding statuses where the team picked a seller and takes it from there. */
    private const LEGACY_MARKED = ['seller marked', 'mark for sell', 'transporter marked', 'process over phone'];

    /**
     * The buyer's progress dot. The auction service owns it for RFQs that
     * went through live bidding; a cancelled RFQ, or one no seller could
     * take, is folded into `cancelled` on top.
     *
     * RFQs from before live bidding (or not yet opened) never had an auction,
     * so "not live, no bid" must not read as "closed without an offer": their
     * stage comes from the status the team moved them through instead.
     */
    public function stage(ProductEnquiry $enquiry): string
    {
        if (self::isCancelled($enquiry->status)) {
            return 'cancelled';
        }

        if (! self::biddingRan($enquiry)) {
            return self::legacyStage($enquiry->status);
        }

        return $this->bidding->stage($enquiry);
    }

    /** Which `enquiry_next_step.*` copy fits; usually just the stage. */
    public function nextStepKey(ProductEnquiry $enquiry, string $stage): string
    {
        if (self::biddingRan($enquiry)) {
            return $stage;
        }

        return match ($stage) {
            'sent'        => 'awaiting_offers',
            'offer_ready' => in_array(strtolower(trim((string) $enquiry->status)), self::LEGACY_MARKED, true)
                ? 'team_contact'
                : 'offer_received',
            default       => $stage,
        };
    }

    public static function biddingRan(ProductEnquiry $enquiry): bool
    {
        return $enquiry->bidding_started_at !== null
            || ! in_array((string) $enquiry->bidding_status, ['', 'draft'], true);
    }

    private static function legacyStage(?string $status): string
    {
        $status = strtolower(trim((string) $status));

        return match (true) {
            $status === 'ordered'                          => 'ordered',
            in_array($status, self::LEGACY_REPLIED, true),
            in_array($status, self::LEGACY_MARKED, true)   => 'offer_ready',
            // pending, "Enquiry Sent To Seller" and anything unrecognised.
            default                                        => 'sent',
        };
    }

    public static function isCancelled(?string $status): bool
    {
        $status = strtolower((string) $status);

        return str_contains($status, 'cancel') || str_contains($status, 'no seller');
    }

    public function summary(ProductEnquiry $enquiry, ChatContext $ctx): array
    {
        return $this->summaryFor($enquiry, $ctx, $this->stage($enquiry));
    }

    public function detail(ProductEnquiry $enquiry, ChatContext $ctx): array
    {
        $stage = $this->stage($enquiry);
        $order = $enquiry->getCommodityProductOrder;
        $stats = $this->bidding->stats($enquiry);

        return array_merge($this->summaryFor($enquiry, $ctx, $stage), [
            'bidding'   => [
                'status'         => (string) ($enquiry->bidding_status ?: 'none'),
                'is_live'        => $this->bidding->isLive($enquiry),
                'ends_at'        => optional($enquiry->bidding_ends_at)->toIso8601String(),
                'seconds_left'   => $this->bidding->secondsLeft($enquiry),
                'best_for_price' => $enquiry->best_for_price !== null ? (float) $enquiry->best_for_price : null,
                'offers_count'   => (int) $stats['responses'],
            ],
            'next_step' => $ctx->t('enquiry_next_step.' . $this->nextStepKey($enquiry, $stage)),
            'order'     => $order ? [
                'id'       => (int) $order->id,
                'order_id' => $order->order_id,
                'href'     => '/orders/' . $order->id,
            ] : null,
        ]);
    }

    private function summaryFor(ProductEnquiry $enquiry, ChatContext $ctx, string $stage): array
    {
        $product = $enquiry->getCommodityProduct;

        return [
            'id'            => (int) $enquiry->id,
            'ref'           => $enquiry->unique_id,
            'status'        => $enquiry->status,
            'status_label'  => $ctx->t('enquiry_stage.' . $stage),
            'stage'         => $stage,
            'product'       => [
                'name'      => $product?->name,
                'thumbnail' => $product && $product->thumbnail ? (imageUrl($product->thumbnail) ?: null) : null,
            ],
            'quantity'      => $enquiry->quantity !== null ? (float) $enquiry->quantity : null,
            'unit_label'    => $enquiry->unit_label,
            'delivery_city' => $enquiry->delivery_city,
            'required_by'   => $enquiry->required_by,
            'created_at'    => optional($enquiry->created_at)->toIso8601String(),
            'href'          => '/rfq/' . $enquiry->id,
        ];
    }
}
