<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\RfqStatus;
use App\Models\CommodityProductOrder;
use App\Models\ProductEnquiry;
use App\Models\SellerProductEnquiry;
use Illuminate\Database\Eloquent\Builder;

/**
 * Dashboard tiles from the canonical status model (B25/B35).
 *
 * Every tile set partitions its total: each order is in exactly one of
 * in progress / dispatched / delivered / cancelled, each RFQ in exactly one
 * of open / quoted / accepted / closed — so the tiles always add up to the
 * total shown beside them. The old dashboards counted `status = 'pending'`
 * and `status = 'complete'`, which the admin flow rarely sets, so tiles were
 * zero while the total was not.
 */
class DashboardStats
{
    /** @return array{total: int, in_progress: int, dispatched: int, delivered: int, cancelled: int, payment_due_count: int, payment_due_amount: float} */
    public function orderTiles(Builder $orders): array
    {
        $total = (clone $orders)->count();
        $dispatched = OrderStatus::scopeGroup(clone $orders, 'dispatched')->count();
        $delivered = OrderStatus::scopeGroup(clone $orders, 'delivered')->count();
        $cancelled = OrderStatus::scopeGroup(clone $orders, 'cancelled')->count();

        $dueQuery = (clone $orders)->where('due_amount', '>', 0)
            ->whereNotIn('status', ['cancel', 'cancelled', 'canceled']);

        return [
            'total'              => $total,
            // Whatever is not dispatched, delivered or cancelled — including any
            // status the enum does not know yet — is still in progress.
            'in_progress'        => max(0, $total - $dispatched - $delivered - $cancelled),
            'dispatched'         => $dispatched,
            'delivered'          => $delivered,
            'cancelled'          => $cancelled,
            'payment_due_count'  => (clone $dueQuery)->count(),
            'payment_due_amount' => round((float) (clone $dueQuery)->sum('due_amount'), 2),
        ];
    }

    /** The buyer's RFQs. @return array{total: int, open: int, quoted: int, accepted: int, closed: int} */
    public function buyerRfqTiles(Builder $rfqs): array
    {
        $total = (clone $rfqs)->count();
        $open = (clone $rfqs)->rfqStatusFilter(RfqStatus::Open->value)->count();
        $quoted = (clone $rfqs)->rfqStatusFilter(RfqStatus::Quoted->value)->count();
        $accepted = (clone $rfqs)->rfqStatusFilter(RfqStatus::Accepted->value)->count();

        return [
            'total'    => $total,
            'open'     => $open,
            'quoted'   => $quoted,
            'accepted' => $accepted,
            // No quotes, expired or cancelled.
            'closed'   => max(0, $total - $open - $quoted - $accepted),
        ];
    }

    /**
     * One seller's RFQs, through their own bids.
     *
     * @return array{total: int, new: int, quoted: int, won: int, lost: int, closed: int}
     */
    public function sellerRfqTiles(int $sellerId): array
    {
        $bids = SellerProductEnquiry::where('user_id', $sellerId)
            ->whereNotNull('product_enquiries_id')
            ->with('getProductEnquiry:id,status,bidding_status,bidding_ends_at')
            ->get(['id', 'product_enquiries_id', 'status', 'for_price', 'base_price']);

        $counts = ['new' => 0, 'quoted' => 0, 'won' => 0, 'lost' => 0, 'closed' => 0];
        foreach ($bids as $bid) {
            $status = RfqStatus::forSeller($bid, $bid->getProductEnquiry);
            $key = match ($status) {
                RfqStatus::Open     => 'new',
                RfqStatus::Quoted   => 'quoted',
                RfqStatus::Accepted => 'won',
                RfqStatus::Lost     => 'lost',
                default             => 'closed',
            };
            $counts[$key]++;
        }

        return ['total' => $bids->count()] + $counts;
    }

    /** The seller's open invitations, newest first — what "New RFQs" lists. */
    public function sellerNewRfqs(int $sellerId, int $limit = 5)
    {
        return ProductEnquiry::query()
            ->whereHas('getSellerProductEnquiries', fn ($q) => $q->where('user_id', $sellerId)
                ->whereRaw('NOT ' . ProductEnquiry::PRICED_BID_SQL))
            ->rfqStatusFilter(RfqStatus::Open->value)
            ->with([
                'getBrand:id,name',
                'getCommodityProduct:id,name,slug,thumbnail,category_id',
                'getCommodityProduct.getCategory:id,name',
            ])
            ->latest()
            ->limit($limit)
            ->get();
    }
}
