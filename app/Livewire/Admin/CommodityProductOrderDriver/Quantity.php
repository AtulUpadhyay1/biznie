<?php

namespace App\Livewire\Admin\CommodityProductOrderDriver;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\CommodityProductOrder;
use App\Models\CommodityProductOrderDriver;

class Quantity extends Component
{
    use WithFileUploads;
    public $page_title = 'Update Vehicle Quantity';
    public $hidden_id, $order_id, $driver_data, $order_data, $quantity = [];
    public $invoice, $show_invoice, $amount, $ebill, $ebill_expiry_date, $transport_receipt, $show_ebill, $show_transport_receipt;

    public function mount($order_id, $id)
    {
        $this->hidden_id = $id;
        $this->order_id = $order_id;

        $this->driver_data = CommodityProductOrderDriver::findOrFail($this->hidden_id);
        $this->order_data = CommodityProductOrder::with('getBrand', 'getCommodityProduct', 'getDrivers', 'getSeller', 'getCustomer', 'getProductEnquiry')->findOrFail($this->order_id);
        $this->show_invoice = imageUrl($this->driver_data->invoice);
        $this->amount = $this->driver_data->amount;
        $this->show_ebill = imageUrl($this->driver_data->ebill);
        $this->ebill_expiry_date = $this->driver_data->ebill_expiry_date;
        $this->show_transport_receipt = imageUrl($this->driver_data->transport_receipt);
        foreach ($this->order_data->value as $key => $variation){
            $this->quantity[$key] = $this->driver_data->final_quantity_by_seller ? $this->driver_data->final_quantity_by_seller[$key] : 0;
        }
    }

    public function render()
    {
        return view('admin.commodity_product_order_driver.quantity');
    }

    public function update()
    {
        foreach ($this->order_data->value as $key => $variation) {
            $this->validate([
                'quantity.'.$key                  => 'required|numeric|min:0',
            ],[
                'quantity.' . $key . '.required' => 'Enter quantity.',
                'quantity.' . $key . '.numeric'  => 'Quantity must be a number.',
                'quantity.' . $key . '.min'      => 'Quantity must be at least 0.',
                'quantity.' . $key . '.regex'    => 'Quantity must be a non-negative number without a leading "+" or "-".',
            ]);
        }

        $this->driver_data->final_quantity_by_seller = $this->quantity;
        $this->driver_data->invoice     = $this->invoice ? imageUpload($this->invoice, 'driver_detail', $this->driver_data->invoice) : $this->driver_data->invoice;
        $this->driver_data->amount      = $this->amount;
        $this->driver_data->save();

        session()->flash('success', 'Data updated successfully !!');
        return $this->redirectRoute('admin.commodity-product-order.show', [$this->order_id] ,navigate: true);
    }
}
