<?php

namespace App\Livewire\Admin\CommodityProductEnquiry;

use App\Models\ProductEnquiry;
use App\Models\SellerCommodityProduct;
use App\Models\SellerProductEnquiry;
use App\Models\User;
use App\Services\Rfq\LiveBiddingService;
use Livewire\Component;

/**
 * "RFQ Details & Manual Price Entry".
 *
 * Not every seller bids through the app - a good number still phone their rate
 * in. Without this screen those sellers simply do not appear on the board, and
 * the buyer is quoted a worse price than the market actually offered. Prices
 * keyed here are marked `manual` so the board always shows which number came
 * from a seller and which came from an operator.
 */
class ManualPriceEntry extends Component
{
    public $page_title = 'RFQ Details & Manual Price Entry';

    public $hidden_id;

    /** Seller row currently loaded into the form. */
    public $selected_bid_id;

    public $seller_search = '';

    /* --- Price form ------------------------------------------------- */
    public $ex_works_price;
    public $freight_charges;
    public $other_charges = 0;
    public $ex_works_city;
    public $ex_works_state;
    public $freight_type = 'doorstep';
    public $remarks;

    /* --- "Add New Seller Price (Manual)" ---------------------------- */
    public $new_seller_id;

    protected $queryString = [
        'selected_bid_id' => ['except' => null, 'as' => 'seller'],
    ];

    public function mount($id): void
    {
        $this->hidden_id = $id;

        $rfq = ProductEnquiry::findOrFail($id);
        app(LiveBiddingService::class)->closeIfExpired($rfq);

        if ($this->selected_bid_id) {
            $this->loadSeller((int) $this->selected_bid_id);
            return;
        }

        $first = SellerProductEnquiry::where('product_enquiries_id', $id)->orderBy('id')->value('id');
        if ($first) {
            $this->loadSeller((int) $first);
        }
    }

    /** Pulls one seller's current numbers into the form. */
    public function loadSeller(int $bidId): void
    {
        $bid = SellerProductEnquiry::where('product_enquiries_id', $this->hidden_id)->find($bidId);
        if (! $bid) {
            return;
        }

        $rfq = ProductEnquiry::find($this->hidden_id);

        $this->selected_bid_id = $bid->id;
        $this->ex_works_price  = $bid->ex_works_price;
        // A seller with no freight yet gets the lane rate pre-filled, so the
        // operator only has to override it when the lookup is wrong.
        $this->freight_charges = $bid->freight_charges
            ?? ($rfq ? app(LiveBiddingService::class)->resolveFreight($rfq, $bid->ex_works_city, $bid->ex_works_state) : 0);
        $this->other_charges   = $bid->other_charges ?? 0;
        $this->ex_works_city   = $bid->ex_works_city;
        $this->ex_works_state  = $bid->ex_works_state;
        $this->freight_type    = $bid->freight_type ?: 'doorstep';
        $this->remarks         = $bid->remarks;

        $this->resetValidation();
    }

    /** Live F.O.R preview under the three inputs. */
    public function getForPriceProperty(): float
    {
        return app(LiveBiddingService::class)->forPrice(
            (float) str_replace(',', '', (string) $this->ex_works_price),
            (float) str_replace(',', '', (string) $this->freight_charges),
            (float) str_replace(',', '', (string) $this->other_charges)
        );
    }

    public function savePrice()
    {
        $this->ex_works_price  = str_replace(',', '', (string) $this->ex_works_price);
        $this->freight_charges = str_replace(',', '', (string) $this->freight_charges);
        $this->other_charges   = str_replace(',', '', (string) $this->other_charges);

        $this->validate([
            'selected_bid_id' => 'required',
            'ex_works_price'  => 'required|numeric|min:0',
            'freight_charges' => 'required|numeric|min:0',
            'other_charges'   => 'nullable|numeric|min:0',
            'ex_works_city'   => 'nullable|string|max:120',
            'freight_type'    => 'required|in:doorstep,ex_works',
            'remarks'         => 'nullable|string|max:500',
        ], [], [
            'selected_bid_id' => 'seller',
            'ex_works_price'  => 'ex-works price',
            'freight_charges' => 'freight charges',
        ]);

        $rfq = ProductEnquiry::find($this->hidden_id);
        $bid = SellerProductEnquiry::where('product_enquiries_id', $this->hidden_id)->find($this->selected_bid_id);

        if (! $rfq || ! $bid) {
            $this->dispatch('alert', type: 'error', message: 'Seller not found on this RFQ.');
            return;
        }

        if (strtolower((string) $rfq->status) === 'ordered') {
            $this->dispatch('alert', type: 'error', message: 'This RFQ has already been converted to an order.');
            return;
        }

        app(LiveBiddingService::class)->recordPrice(
            bid: $bid,
            exWorks: (float) $this->ex_works_price,
            freight: (float) $this->freight_charges,
            other: (float) ($this->other_charges ?: 0),
            source: 'manual',
            adminId: auth('admin')->id(),
            remarks: $this->remarks,
            exWorksCity: $this->ex_works_city,
            exWorksState: $this->ex_works_state,
            freightType: $this->freight_type
        );

        $this->dispatch('alert', type: 'success', message: 'Price saved and the board has been re-ranked.');
    }

    /** Clears the form back to the stored values for the selected seller. */
    public function cancelEdit(): void
    {
        if ($this->selected_bid_id) {
            $this->loadSeller((int) $this->selected_bid_id);
        }
    }

    /**
     * Brings a seller who was not auto-matched onto the board.
     *
     * Matching is by catalogue listing, which misses sellers who stock the
     * product without having listed it. An operator who knows they do should
     * not have to wait for the catalogue to catch up.
     */
    public function addSeller(): void
    {
        $this->validate(['new_seller_id' => 'required|integer'], [], ['new_seller_id' => 'seller']);

        $rfq = ProductEnquiry::find($this->hidden_id);
        if (! $rfq) {
            return;
        }

        $already = SellerProductEnquiry::where('product_enquiries_id', $rfq->id)
            ->where('user_id', $this->new_seller_id)
            ->exists();

        if ($already) {
            $this->dispatch('alert', type: 'error', message: 'That seller is already on this RFQ.');
            return;
        }

        $bid = app(LiveBiddingService::class)->attachSeller($rfq, (int) $this->new_seller_id);

        $this->new_seller_id = null;
        // Handled by the shared listener in biznie-admin.js.
        $this->dispatch('bz-modal-close', id: 'addSellerModal');

        if ($bid) {
            $this->loadSeller($bid->id);
            $this->dispatch('alert', type: 'success', message: 'Seller added. Enter their price to put them on the board.');
        }
    }

    public function notifyAllSellers(): void
    {
        $rfq = ProductEnquiry::find($this->hidden_id);
        if (! $rfq) {
            return;
        }

        $count = app(LiveBiddingService::class)->notifyAllSellers($rfq);

        $this->dispatch('alert', type: 'success', message: $count . ' seller(s) notified.');
    }

    public function extendTimer(): void
    {
        $rfq = ProductEnquiry::find($this->hidden_id);
        if (! $rfq) {
            return;
        }

        if (strtolower((string) $rfq->status) === 'ordered') {
            $this->dispatch('alert', type: 'error', message: 'This RFQ has already been converted to an order.');
            return;
        }

        $minutes = (int) config('biznie.rfq.extend_minutes', 5);
        app(LiveBiddingService::class)->extend($rfq, $minutes);

        $this->dispatch('alert', type: 'success', message: 'Bidding extended by ' . $minutes . ' minutes.');
    }

    public function render()
    {
        $bidding = app(LiveBiddingService::class);

        $rfq = ProductEnquiry::with(['getBrand:id,name', 'getCommodityProduct:id,name', 'getUser:id,name'])
            ->findOrFail($this->hidden_id);

        $sellers = SellerProductEnquiry::where('product_enquiries_id', $rfq->id)
            ->with(['getUser:id,name', 'getUser.getUserDetail:id,user_id,company_name'])
            ->when($this->seller_search !== '', function ($q) {
                $term = '%' . $this->seller_search . '%';
                $q->where(function ($sub) use ($term) {
                    $sub->where('ex_works_city', 'like', $term)
                        ->orWhereHas('getUser', fn ($u) => $u->where('name', 'like', $term));
                });
            })
            ->orderByRaw('for_price IS NULL, for_price ASC')
            ->orderBy('id')
            ->get();

        // Resolved independently of `$sellers`: that list is filtered by the
        // search box, and looking the selected row up inside it meant typing a
        // search that excluded it blanked the form mid-edit.
        $selected = $this->selected_bid_id
            ? SellerProductEnquiry::where('product_enquiries_id', $rfq->id)
                ->with(['getUser:id,name', 'getUser.getUserDetail:id,user_id,company_name'])
                ->find($this->selected_bid_id)
            : null;

        $board = $bidding->leaderboard($rfq);
        $stats = $bidding->stats($rfq);

        // Sellers who list this product but are not on the board yet.
        $attachedIds = SellerProductEnquiry::where('product_enquiries_id', $rfq->id)->pluck('user_id');

        $candidateIds = SellerCommodityProduct::where('commodity_product_id', $rfq->commodity_product_id)
            ->when($rfq->brand_id, fn ($q) => $q->where('brand_id', $rfq->brand_id))
            ->whereNotIn('user_id', $attachedIds)
            ->pluck('user_id')
            ->unique();

        $candidates = User::whereIn('id', $candidateIds)
            ->where('type', 'seller')
            ->with('getUserDetail:id,user_id,company_name')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.commodity_product_enquiry.manual_price_entry', compact(
            'rfq',
            'sellers',
            'selected',
            'board',
            'stats',
            'candidates',
            'bidding'
        ));
    }
}
