<?php

namespace App\Livewire\Admin\CommodityProductOrder;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\CommodityProductOrder;

class Status extends Component
{
    public $page_title = 'Order Status';
    public $hidden_id, $data, $status;

    public function mount($id)
    {
        $this->hidden_id = $id;
        $this->data = CommodityProductOrder::with('getBrand', 'getCommodityProduct', 'getDrivers', 'getSeller', 'getCustomer')->findOrFail($this->hidden_id);
        $this->status = $this->data->status;
    }

    public function render()
    {
        $this->page_title = 'Order Status For - '. $this->data->order_id;
        return view('admin.commodity_product_order.status');
    }

    public function updateStatus()
    {
        $this->data->status = $this->status;
        $history = $this->data->history;
        $history[] = ['status' => 'Order ' .ucwords($this->status). ' By Admin', 'created_at' => Carbon::now()];
        $this->data->history = $history;
        $this->data->save();
        $this->dispatch('alert',
            type : 'success',
            message : 'Status updated successfully.',
        );
    }
}
