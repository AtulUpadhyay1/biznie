<?php

namespace App\Livewire\Admin\IngotPrice;

use Livewire\Component;
use App\Models\IngotPrice;
use App\Models\IngotPriceLocation;

class Edit extends Component
{
    public $hidden_id, $location, $price, $date_time;
    public $product = IngotPrice::DEFAULT_PRODUCT, $source;

    public function mount($id)
    {
        $this->hidden_id = $id;
        $data = IngotPrice::findOrFail($this->hidden_id);
        $this->product = $data->product ?: IngotPrice::DEFAULT_PRODUCT;
        $this->source = $data->source;
        $this->location = $data->location;
        $this->price = $data->price;
        $this->date_time = $data->date_time;
    }

    public function render()
    {
        $location_list = IngotPriceLocation::orderBy('location', 'ASC')->get();
        $product_list = IngotPrice::PRODUCTS;
        return view('admin.ingot_price.form', compact('location_list', 'product_list'), ['page_title' => 'Edit Ingot Price']);
    }

    public function update()
    {
        $this->validate([
            'product'   => 'required|in:' . implode(',', IngotPrice::PRODUCTS),
            'location'  => 'required',
            'price'     => 'required|numeric|gt:0',
            'source'    => 'nullable|string|max:160',
            'date_time' => 'required|date',
        ]);

        $data = IngotPrice::findOrFail($this->hidden_id);
        $data->product = $this->product;
        $data->location = $this->location;
        $data->source = $this->source ?: null;
        $data->price = $this->price;
        $data->date_time = $this->date_time;
        $data->save();

        session()->flash('success', 'Ingot price updated successfully !!');
        return $this->redirectRoute('admin.ingot_price.index', navigate: true);
    }
}
