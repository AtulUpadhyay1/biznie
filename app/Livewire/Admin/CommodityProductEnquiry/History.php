<?php

namespace App\Livewire\Admin\CommodityProductEnquiry;

use Livewire\Component;
use App\Models\ProductEnquiry;

class History extends Component
{
    #[Url(history: true)]
    public $page_title = 'Enquiry history';
    public $hidden_id;

    public function mount($id)
    {
        $this->hidden_id = $id;
    }

    public function render()
    {
        $data = ProductEnquiry::with('getBrand', 'getCommodityProduct')->findOrFail($this->hidden_id);
        return view('admin.commodity_product_enquiry.history', compact('data'));
    }
}
