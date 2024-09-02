<?php

namespace App\Livewire\Admin\CommodityProductOrderDriver;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\CommodityProductOrderDriver;

class Edit extends Component
{
    use WithFileUploads;
    public $page_title = 'Edit Vehicle';

    public $hidden_id, $order_id, $name, $phone, $alternate_phone_number, $vehicle_number, $tracking_number, $transporter_name, $transporter_phone_number, $advance_amount, $driver_photo, $unloaded_vehicle_photo, $loaded_vehicle_photo, $driver_with_vehicle_photo, $invoice, $ebill, $ebill_expiry_date, $transport_receipt;
    public $show_driver_photo, $show_unloaded_vehicle_photo, $show_loaded_vehicle_photo, $show_driver_with_vehicle_photo, $show_invoice, $show_ebill, $show_transport_receipt;

    public function mount($order_id, $id)
    {
        $this->hidden_id = $id;
        $this->order_id = $order_id;

        $data = CommodityProductOrderDriver::findOrFail($this->hidden_id);
        $this->name                         = $data->name;
        $this->phone                        = $data->phone;
        $this->alternate_phone_number       = $data->alternate_phone_number;
        $this->vehicle_number               = $data->vehicle_number;
        $this->tracking_number              = $data->tracking_number;
        $this->transporter_name             = $data->transporter_name;
        $this->transporter_phone_number     = $data->transporter_phone_number;
        $this->advance_amount               = $data->advance_amount;
        $this->ebill_expiry_date            = $data->ebill_expiry_date;
        $this->show_driver_photo            = imageUrl($data->photo);
        $this->show_unloaded_vehicle_photo  = imageUrl($data->unloaded_vehicle_photo);
        $this->show_loaded_vehicle_photo    = imageUrl($data->loaded_vehicle_photo);
        $this->show_driver_with_vehicle_photo = imageUrl($data->driver_with_vehicle_photo);
        $this->show_invoice                 = imageUrl($data->invoice);
        $this->show_ebill                   = imageUrl($data->ebill);
        $this->show_transport_receipt       = imageUrl($data->transport_receipt);
    }

    public function render()
    {
        return view('admin.commodity_product_order_driver.form');
    }

    public function update()
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

        $data = CommodityProductOrderDriver::findOrFail($this->hidden_id);
        $data->order_id     = $this->order_id;
        $data->name         = $this->name;
        $data->phone        = $this->phone;
        $data->photo        = $this->driver_photo ? imageUpload($this->driver_photo, 'driver_detail', $data->photo) : $data->photo;
        $data->unloaded_vehicle_photo      = $this->unloaded_vehicle_photo ? imageUpload($this->unloaded_vehicle_photo, 'driver_detail', $data->unloaded_vehicle_photo) : $data->unloaded_vehicle_photo;
        $data->loaded_vehicle_photo        = $this->loaded_vehicle_photo ? imageUpload($this->loaded_vehicle_photo, 'driver_detail', $data->loaded_vehicle_photo) : $data->loaded_vehicle_photo;
        $data->driver_with_vehicle_photo   = $this->driver_with_vehicle_photo ? imageUpload($this->driver_with_vehicle_photo, 'driver_detail', $data->driver_with_vehicle_photo) : $data->driver_with_vehicle_photo;
        $data->vehicle_number   = $this->vehicle_number;
        $data->tracking_number  = $this->tracking_number;
        $data->invoice          = $this->invoice ? imageUpload($this->invoice, 'driver_detail', $data->invoice) : $data->invoice;
        $data->ebill            = $this->ebill ? imageUpload($this->ebill, 'driver_detail', $data->ebill) : $data->ebill;
        $data->ebill_expiry_date= $this->ebill_expiry_date;
        $data->transport_receipt= $this->transport_receipt ? imageUpload($this->transport_receipt, 'driver_detail', $data->transport_receipt) : $data->transport_receipt;
        $data->alternate_phone_number  = $this->alternate_phone_number;
        $data->transporter_name  = $this->transporter_name;
        $data->transporter_phone_number  = $this->transporter_phone_number;
        $data->advance_amount  = $this->advance_amount;
        $data->save();

        session()->flash('success', 'Vehicle updated successfully !!');
        return $this->redirectRoute('admin.commodity-product-order.show', $this->order_id ,navigate: true);
    }
}
