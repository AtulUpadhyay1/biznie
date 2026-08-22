<?php

namespace App\Livewire\Admin\CommodityProductEnquiry;

use App\Models\ProductEnquiry;
use App\Models\SellerProductEnquiry;
use App\Services\Rfq\LiveBiddingService;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Operations view over every RFQ auction.
 *
 * Separate from the existing enquiry list because the two answer different
 * questions: that one is "what has this customer ever asked for", this one is
 * "which auctions need a human right now" - no responses, minutes left, or a
 * seller who only quotes over the phone.
 */
class LiveBidding extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $page_title = 'Live RFQs';

    public $search = '';

    /** all | live | closed | awarded */
    public $filter = 'live';

    protected $queryString = [
        'search' => ['except' => ''],
        'filter' => ['except' => 'live'],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function setFilter(string $filter): void
    {
        $this->filter = $filter;
        $this->resetPage();
    }

    public function render()
    {
        $bidding = app(LiveBiddingService::class);

        $list = ProductEnquiry::search($this->search)
            ->with(['getBrand:id,name', 'getCommodityProduct:id,name', 'getUser:id,name'])
            ->when($this->filter === 'live', fn ($q) => $q->liveBidding())
            ->when($this->filter === 'closed', fn ($q) => $q->where(function ($sub) {
                $sub->where('bidding_status', 'closed')
                    ->orWhere(function ($expired) {
                        $expired->where('bidding_status', 'live')->where('bidding_ends_at', '<=', now());
                    });
            }))
            ->when($this->filter === 'awarded', fn ($q) => $q->where('status', 'ordered'))
            ->whereNotNull('bidding_started_at')
            // Closing soonest first: the auction with two minutes left is the
            // one worth looking at.
            ->orderByRaw('bidding_ends_at IS NULL, bidding_ends_at ASC')
            ->latest('id')
            ->paginate(getPaginate());

        // Invitation and response counts for the whole page in two queries
        // rather than two per row.
        $ids = $list->getCollection()->pluck('id');

        $invited = SellerProductEnquiry::whereIn('product_enquiries_id', $ids)
            ->selectRaw('product_enquiries_id, COUNT(*) as total')
            ->groupBy('product_enquiries_id')
            ->pluck('total', 'product_enquiries_id');

        $responded = SellerProductEnquiry::whereIn('product_enquiries_id', $ids)
            ->whereNotNull('for_price')
            ->selectRaw('product_enquiries_id, COUNT(*) as total')
            ->groupBy('product_enquiries_id')
            ->pluck('total', 'product_enquiries_id');

        $counts = [
            'live'    => ProductEnquiry::liveBidding()->count(),
            'closed'  => ProductEnquiry::whereNotNull('bidding_started_at')
                ->where(function ($q) {
                    $q->where('bidding_status', 'closed')
                        ->orWhere(function ($expired) {
                            $expired->where('bidding_status', 'live')->where('bidding_ends_at', '<=', now());
                        });
                })->count(),
            'awarded' => ProductEnquiry::whereNotNull('bidding_started_at')->where('status', 'ordered')->count(),
            'all'     => ProductEnquiry::whereNotNull('bidding_started_at')->count(),
        ];

        return view('admin.commodity_product_enquiry.live_bidding', compact('list', 'invited', 'responded', 'counts', 'bidding'));
    }
}
