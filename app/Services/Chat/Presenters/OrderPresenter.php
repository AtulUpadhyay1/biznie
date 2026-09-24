<?php

namespace App\Services\Chat\Presenters;

use App\Models\CommodityProductOrder;
use App\Services\Chat\ChatContext;
use Carbon\Carbon;

/** CommodityProductOrder → ChatOrderSummary / ChatOrderDetail (plan §4.3). */
class OrderPresenter
{
    /** Order `status` (lower-cased) → stepper stage. Plan §4.4. */
    private const STAGES = [
        'pending'                 => 'placed',
        'confirm'                 => 'confirmed',
        'vehicle booked'          => 'loading',
        'vehicle waiting to load' => 'loading',
        'loading'                 => 'loading',
        'bills generated'         => 'loading',
        'dispatched'              => 'dispatched',
        'delivered'               => 'delivered',
        'complete'                => 'completed',
        'cancel'                  => 'cancelled',
    ];

    /** Statuses after which an order no longer needs watching. */
    public const CLOSED_STATUSES = ['delivered', 'complete', 'cancel'];

    public const LIST_RELATIONS = [
        'getBrand:id,name',
        'getCommodityProduct:id,name,slug,thumbnail',
        'getProductEnquiry:id,unique_id',
    ];

    public const DETAIL_RELATIONS = [
        'getBrand:id,name',
        'getCommodityProduct:id,name,slug,thumbnail',
        'getProductEnquiry:id,unique_id',
        'getDrivers',
    ];

    public function stage(?string $status): string
    {
        return self::STAGES[strtolower(trim((string) $status))] ?? 'placed';
    }

    public function summary(CommodityProductOrder $order, ChatContext $ctx): array
    {
        $known = isset(self::STAGES[strtolower(trim((string) $order->status))]);
        $stage = $this->stage($order->status);

        // Unknown statuses still sit on the "placed" dot, but the buyer is
        // shown the raw status rather than a label that would misdescribe it.
        $label = $known || ! $order->status
            ? $ctx->t('order_stage.' . $stage)
            : ucwords((string) $order->status);

        $product = $order->getCommodityProduct;

        return [
            'id'           => (int) $order->id,
            'order_id'     => $order->order_id,
            'rfq_ref'      => $order->getProductEnquiry?->unique_id,
            'status'       => $order->status,
            'status_label' => $label,
            'stage'        => $stage,
            'product'      => [
                'name'      => $product?->name,
                'thumbnail' => $product && $product->thumbnail ? (imageUrl($product->thumbnail) ?: null) : null,
            ],
            'brand'        => $order->getBrand?->name,
            'total_amount' => self::money($order->total_amount),
            'due_amount'   => self::money($order->due_amount),
            'created_at'   => optional($order->created_at)->toIso8601String(),
            'href'         => '/orders/' . $order->id,
        ];
    }

    public function detail(CommodityProductOrder $order, ChatContext $ctx): array
    {
        return array_merge($this->summary($order, $ctx), [
            'paid_amount'   => self::money($order->paid_amount),
            'final_amount'  => self::money($order->final_amount),
            'origin_city'   => $order->origin_city,
            'timeline'      => $this->timeline($order),
            'tracking'      => $order->trackingRows(),
            'cancel_reason' => $order->cancel_reason ?: null,
            'ledger_href'   => '/orders/' . $order->id . '/ledger',
        ]);
    }

    /** @return list<array{label: string, at: ?string}> */
    private function timeline(CommodityProductOrder $order): array
    {
        $rows = [];
        foreach (is_array($order->history) ? $order->history : [] as $entry) {
            if (! is_array($entry) || empty($entry['status'])) {
                continue;
            }
            $rows[] = [
                'label' => ucwords((string) $entry['status']),
                'at'    => self::iso($entry['created_at'] ?? null),
            ];
        }

        return $rows;
    }

    private static function money(mixed $value): ?float
    {
        return $value === null || $value === '' ? null : round((float) $value, 2);
    }

    private static function iso(mixed $value): ?string
    {
        if (! is_string($value) || $value === '') {
            return null;
        }

        try {
            return Carbon::parse($value)->toIso8601String();
        } catch (\Throwable) {
            return null;
        }
    }
}
