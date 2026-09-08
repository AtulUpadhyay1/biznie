<?php

namespace App\Livewire\Admin\SellerProduct;

use Livewire\Component;
use Illuminate\Support\Str;
use App\Models\PackagingType;
use App\Models\SellerCommodityProduct;

class Edit extends Component
{
    public $user_id, $product_id;
    public $name;
    public $packaging_type = [], $packaging_type_name = [], $packaging_type_price = [];
    public $loading_address = [];
    public $commission_type = 'exclude', $commission_amount = 0;

    /**
     * Which prices this listing shows on the storefront.
     *
     * The seller dashboard could already set these; the admin had no way to,
     * which is why "Basic" could not be turned on for any listing from here.
     */
    public $show_basic_price = false, $show_ex_price = false, $show_for_price = false, $show_fob_price = false;

    public function mount($user_id, $product_id)
    {
        $this->user_id      = $user_id;
        $this->product_id   = $product_id;


        $data = SellerCommodityProduct::findOrFail($product_id);
        $this->name                 = $data->name;
        $this->packaging_type       = $data->packaging_type;
        $this->packaging_type_price = $data->packaging_type_price;
        $this->loading_address      = $data->loading_address;
        $this->commission_type      = $data->commission_type;
        $this->commission_amount    = $data->commission_amount;
        $this->show_basic_price     = (bool) $data->show_basic_price;
        $this->show_ex_price        = (bool) $data->show_ex_price;
        $this->show_for_price       = (bool) $data->show_for_price;
        $this->show_fob_price       = (bool) $data->show_fob_price;
    }

    public function render()
    {
        $packaging_type_list = PackagingType::active()->orderBy('name', 'asc')->get();
        $this->packaging_type_name = PackagingType::whereIn('id', $this->packaging_type)->pluck('name');

        return view('admin.seller_product.edit', compact('packaging_type_list'), ['page_title' => 'Edit Product']);
    }

    public function save()
    {
        $this->validate([
            'name'  => 'required',
            'commission_type'   => 'required|in:exclude,include',
            'commission_amount' => 'required|numeric|min:0',
        ]);
        try {
            $data = SellerCommodityProduct::findOrFail($this->product_id);
            $data->name = $this->name;
            $data->slug = Str::slug($this->name);
            $data->packaging_type = $this->packaging_type ?? [];
            $data->packaging_type_price = $this->packaging_type_price ?? [];
            $data->loading_address = $this->loading_address ?? [];
            $data->commission_type      = $this->commission_type;
            $data->commission_amount    = $this->commission_amount;
            $data->show_basic_price     = (bool) $this->show_basic_price;
            $data->show_ex_price        = (bool) $this->show_ex_price;
            $data->show_for_price       = (bool) $this->show_for_price;
            $data->show_fob_price       = (bool) $this->show_fob_price;
            $data->save();

            $this->dispatch('alert',
                type : 'success',
                message : 'Product updated successfully.',
            );

        } catch (\Throwable $th) {
            $this->dispatch('alert',
                type : 'error',
                message : 'Something went wrong. Please try again later.',
            );
        }
    }
}
