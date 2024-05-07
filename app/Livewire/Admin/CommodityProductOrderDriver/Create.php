<?php

namespace App\Livewire\Admin\CommodityProductOrderDriver;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\CommodityProductOrderDriver;

class Create extends Component
{
    use WithFileUploads;
    public $page_title = 'Add Driver';

    public $hidden_id, $order_id, $name, $phone, $alternate_phone_number, $vehicle_number, $tracking_number, $transporter_name, $transporter_phone_number, $advance_amount, $driver_photo, $unloaded_vehicle_photo, $loaded_vehicle_photo, $driver_with_vehicle_photo, $invoice, $ebill, $transport_receipt;
    public $show_driver_photo, $show_unloaded_vehicle_photo, $show_loaded_vehicle_photo, $show_driver_with_vehicle_photo, $show_invoice, $show_ebill, $show_transport_receipt;

    public function mount($order_id)
    {
        $this->order_id = $order_id;
    }

    public function render()
    {
        return view('admin.commodity_product_order_driver.form');
    }

    public function save()
    {
        $this->validate([
            'name'                  => 'required',
            'phone'                 => 'required|numeric|digits:10',
            'alternate_phone_number'=> 'nullable|numeric|digits:10',
            'tracking_number'       => 'required',
            'transporter_name'      => 'required',
            'vehicle_number'        => 'required',
            'transporter_phone_number'=> 'required|numeric|digits:10',
            'advance_amount'        => 'required|numeric',
        ]);

        $data               = new CommodityProductOrderDriver;
        $data->order_id     = $this->order_id;
        $data->name         = $this->name;
        $data->phone        = $this->phone;
        $data->photo        = $this->driver_photo ? imageUpload($this->driver_photo, 'driver_detail') : NULL;
        $data->unloaded_vehicle_photo      = $this->unloaded_vehicle_photo ? imageUpload($this->unloaded_vehicle_photo, 'driver_detail') : NULL;
        $data->loaded_vehicle_photo        = $this->loaded_vehicle_photo ? imageUpload($this->loaded_vehicle_photo, 'driver_detail') : NULL;
        $data->driver_with_vehicle_photo   = $this->driver_with_vehicle_photo ? imageUpload($this->driver_with_vehicle_photo, 'driver_detail') : NULL;
        $data->vehicle_number   = $this->vehicle_number;
        $data->tracking_number  = $this->tracking_number;
        $data->invoice          = $this->invoice ? imageUpload($this->invoice, 'driver_detail') : NULL;
        $data->ebill            = $this->ebill ? imageUpload($this->ebill, 'driver_detail') : NULL;
        $data->transport_receipt= $this->transport_receipt ? imageUpload($this->transport_receipt, 'driver_detail') : NULL;
        $data->alternate_phone_number  = $this->alternate_phone_number;
        $data->transporter_name  = $this->transporter_name;
        $data->transporter_phone_number  = $this->transporter_phone_number;
        $data->advance_amount  = $this->advance_amount;
        $data->save();

        session()->flash('success', 'Driver added successfully !!');
        return $this->redirectRoute('admin.commodity-product-order.show', $this->order_id ,navigate: true);
    }
}
