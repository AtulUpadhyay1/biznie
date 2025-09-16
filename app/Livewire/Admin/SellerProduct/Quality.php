<?php

namespace App\Livewire\Admin\SellerProduct;

use Livewire\Component;
use App\Models\SellerCommodityProduct;

class Quality extends Component
{
    public $user_id, $product_id;
    public $quality = [], $quality_price = [];
    public $selected_quality = [], $selected_quality_price = [];

    public function mount($user_id, $product_id)
    {
        $this->user_id      = $user_id;
        $this->product_id   = $product_id;
        $data = SellerCommodityProduct::with('getCommodityProduct')->find($this->product_id);
        if($data){
            $this->quality        = $data->getCommodityProduct->quality ?? [];
            $this->quality_price  = $data->getCommodityProduct->quality_price ?? [];

            $this->selected_quality       = $data->quality ?? [];
            $savedPrices = $data->quality_price ?? [];

            $priceArray = [];
            foreach ($this->quality as $key => $qualityItem) {
                if (isset($savedPrices[$key])) {
                    $priceArray[$qualityItem] = $savedPrices[$key];
                } else {
                    $priceArray[$qualityItem] = $this->quality_price[$key] ?? '';
                }
            }
            $this->selected_quality_price = $priceArray;
        }
    }

    public function updatedSelectedQuality()
    {
        $newPrices = [];
        foreach ($this->selected_quality as $selectedQual) {
            if (isset($this->selected_quality_price[$selectedQual])) {
                $newPrices[$selectedQual] = $this->selected_quality_price[$selectedQual];
            } else {
                $qualityIndex = array_search($selectedQual, $this->quality);
                if ($qualityIndex !== false) {
                    $newPrices[$selectedQual] = $this->quality_price[$qualityIndex] ?? '';
                }
            }
        }
        $this->selected_quality_price = $newPrices;
    }

    public function save()
    {
        $this->validate([
            'selected_quality' => 'required|array|min:1',
            'selected_quality_price.*' => 'required|numeric|min:0',
        ], [
            'selected_quality.required' => 'Please select at least one quality.',
            'selected_quality.min' => 'Please select at least one quality.',
            'selected_quality_price.*.required' => 'Price is required for selected quality.',
            'selected_quality_price.*.numeric' => 'Price must be a valid number.',
            'selected_quality_price.*.min' => 'Price cannot be negative.',
        ]);
        $filteredPrices = [];
        foreach ($this->selected_quality as $quality) {
            if (isset($this->selected_quality_price[$quality])) {
                $filteredPrices[] = $this->selected_quality_price[$quality];
            }
        }

        $product = SellerCommodityProduct::find($this->product_id);
        if (!$product) {
            $this->dispatch('alert',
                type : 'error',
                message : 'Something went wrong. Please try again later.',
            );
            return;
        }
        $product->quality = $this->selected_quality ?? [];
        $product->quality_price = $filteredPrices ?? [];
        $product->save();
        session()->flash('message', 'Product quality updated successfully!');
        return redirect()->route('admin.seller-product.index', $this->user_id);
    }

    public function render()
    {
        return view('admin.seller_product.quality', ['page_title' => 'Product Quality']);
    }
}
