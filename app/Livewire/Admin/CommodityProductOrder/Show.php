<?php

namespace App\Livewire\Admin\CommodityProductOrder;

use PDF;
use Livewire\Component;
use App\Models\CommodityProductOrder;
use Illuminate\Support\Facades\Storage;

class Show extends Component
{
    public $page_title = 'View Order';
    public $hidden_id = '';
    public function mount($id)
    {
        $this->hidden_id = $id;
    }

    public function render()
    {
        $data = CommodityProductOrder::with('getBrand', 'getCommodityProduct', 'getDrivers', 'getSeller', 'getCustomer')->findOrFail($this->hidden_id);
        $this->page_title = 'View Order '. $data->order_id;
        return view('admin.commodity_product_order.show', compact('data'));
    }

    public function invoicePrint()
    {
        $order_detail = CommodityProductOrder::with('getBrand', 'getCommodityProduct', 'getDrivers', 'getSeller', 'getCustomer')->findOrFail($this->hidden_id);
        $data = [
            'order_detail' => $order_detail
        ];

        // Generate PDF content
        $pdfContent = PDF::loadView('admin.commodity_product_order.print_invoice', $data)->output();

        // Save PDF to storage
        $filePath = 'public/order_invoce/'.$order_detail->order_id . '_invoice.pdf';
        Storage::put($filePath, $pdfContent);

        // Get public URL
        $publicUrl = Storage::url($filePath);

        // Redirect to the public URL
        return redirect($publicUrl);


        // $order_detail = CommodityProductOrder::with('getBrand', 'getCommodityProduct', 'getDrivers', 'getSeller', 'getCustomer')->findOrFail($this->hidden_id);
        // $data = [
        //     'order_detail' => $order_detail
        // ];

        // $pdfContent = PDF::loadView('admin.commodity_product_order.print_invoice', $data)->save(public_path() . '/'.$order_detail->order_id.'_invoice.pdf');
        // return redirect($order_detail->order_id.'_invoice.pdf');
    }
}
