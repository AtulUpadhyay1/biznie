<?php

namespace App\Livewire\Admin\SellerProduct;

use App\Models\Address;
use App\Models\SellerCommodityProduct;
use App\Models\SellerProductForPrice;
use App\Models\User;
use App\Services\ProductPricingService;
use Livewire\Component;

/**
 * A seller's city-wise F.O.R (doorstep) prices for one listing.
 *
 * The same rows the seller manages from their own panel. Where a city is priced
 * here it replaces the calculated F.O.R (ex-works + freight) for buyers there —
 * see ProductPricingService::sellerForPrice().
 */
class ForPrice extends Component
{
    public $user_id, $product_id;
    public $page_title = 'F.O.R Price';
    public $seller_name = '';

    public $state = '', $city = '', $price = '';

    public function mount($user_id, $product_id)
    {
        $this->user_id = $user_id;
        $this->product_id = $product_id;

        $user = User::with('getBusiness')->find($user_id);
        $this->seller_name = trim(($user->getBusiness->name ?? '') ?: ($user->name ?? ''));
    }

    /**
     * Picking a state clears the city, since the old one almost certainly does
     * not belong to the new state.
     */
    public function updatedState()
    {
        $this->city = '';
        $this->price = '';
    }

    /**
     * Selecting an already-priced city fills the input with what it is priced
     * at, so the admin edits the number rather than guessing it.
     */
    public function updatedCity()
    {
        $row = $this->existingRow();
        $this->price = $row ? (string) $row->price : '';
    }

    public function save()
    {
        $this->validate([
            'state' => 'required|string|max:120',
            'city'  => 'required|string|max:120',
            'price' => 'required|numeric|min:0',
        ], [
            'state.required' => 'Please select a state.',
            'city.required'  => 'Please select a city.',
            'price.required' => 'Please enter the freight per MT.',
        ]);

        $product = SellerCommodityProduct::where('user_id', $this->user_id)->find($this->product_id);

        if (! $product) {
            $this->dispatch('alert', type: 'error', message: 'Product not found.');

            return;
        }

        $existing = $this->existingRow();

        // Upsert on (product, state, city) — the same contract as the seller's
        // own API, so a city can never end up with two prices.
        SellerProductForPrice::updateOrCreate(
            [
                'product_id' => $product->id,
                'state'      => $this->state,
                'city'       => $this->city,
            ],
            [
                'user_id' => $product->user_id,
                'price'   => $this->price,
            ]
        );

        $this->dispatch('alert',
            type: 'success',
            message: $existing
                ? 'Freight updated for '.$this->city.'.'
                : 'Freight added for '.$this->city.'.',
        );
    }

    /**
     * Drops one destination's price so that city falls back to the calculated
     * F.O.R. Without this a mistyped city would keep overriding it.
     */
    public function delete($id)
    {
        $row = SellerProductForPrice::where('product_id', $this->product_id)->find($id);

        if (! $row) {
            $this->dispatch('alert', type: 'error', message: 'F.O.R price not found.');

            return;
        }

        $city = $row->city;
        $row->delete();

        if ($this->city === $city) {
            $this->price = '';
        }

        $this->dispatch('alert', type: 'success', message: 'F.O.R price removed for '.$city.'.');
    }

    public function render()
    {
        $product = SellerCommodityProduct::with('getBrand', 'getUnit')->find($this->product_id);

        // The other half of every F.O.R price on this screen — priced by the
        // same service the storefront uses, so the arithmetic shown here is the
        // arithmetic a buyer gets.
        $exPrice = $product
            ? (float) app(ProductPricingService::class)->exWorksBreakup($product)['ex_price']
            : 0.0;

        return view('admin.seller_product.for_price', [
            'page_title' => $this->page_title,
            'product'    => $product,
            'ex_price'   => $exPrice,
            'list'       => SellerProductForPrice::where('product_id', $this->product_id)
                ->orderBy('state')->orderBy('city')->get(),
            'state_list' => $this->stateList(),
            'city_list'  => $this->cityList(),
        ]);
    }

    private function existingRow(): ?SellerProductForPrice
    {
        if (! $this->state || ! $this->city) {
            return null;
        }

        return SellerProductForPrice::where('product_id', $this->product_id)
            ->where('state', $this->state)
            ->where('city', $this->city)
            ->first();
    }

    /**
     * States from the address book, each listed once.
     *
     * `addresses` is a pincode table — thousands of rows per state — so both
     * lists are grouped in the database rather than after loading.
     */
    private function stateList()
    {
        return Address::query()
            ->whereNotNull('state')->where('state', '!=', '')
            ->select('state')->distinct()->orderBy('state')->pluck('state');
    }

    private function cityList()
    {
        if (! $this->state) {
            return collect();
        }

        return Address::query()
            ->where('state', $this->state)
            ->whereNotNull('city')->where('city', '!=', '')
            ->select('city')->distinct()->orderBy('city')->pluck('city');
    }
}
