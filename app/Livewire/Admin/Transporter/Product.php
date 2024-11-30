<?php

namespace App\Livewire\Admin\Transporter;

use Livewire\Component;
use App\Models\CommodityProduct;
use App\Models\TransporterDetail;

class Product extends Component
{
    public $page_title = 'Assign Commodity Product';
    public $hidden_id, $commodity_product = [];

    public function mount($id)
    {
        $this->hidden_id      = $id;
        $transporter = TransporterDetail::where('user_id', $this->hidden_id)->firstOrFail();
        $this->commodity_product = $transporter->commodity_product ?? [];
    }

    public function render()
    {
        $commodity_list = CommodityProduct::active()->latest()->with('getCategory')->get();
        return view('admin.transporter.product', compact('commodity_list'));
    }

    public function assignProduct()
    {
        $transporter = TransporterDetail::where('user_id', $this->hidden_id)->first();
        $transporter->commodity_product = $this->commodity_product;
        $transporter->save();
        session()->flash('success', 'Vehicle assigned successfully.');
        return $this->redirectRoute('admin.transporter.product', $this->hidden_id, navigate: true);
    }
}
