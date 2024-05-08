<?php

namespace App\Livewire\Admin\CommodityProductOrder;

use PDF;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\CommodityProductOrder;
use Illuminate\Support\Facades\Storage;
use App\Models\CommodityProductOrderDriver;

class Show extends Component
{
    use WithFileUploads;
    public $page_title = 'View Order';
    public $hidden_id, $upload_type, $uploaded_file, $generate_invoice;

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

    public function setUploadType($type)
    {
        $this->upload_type = $type;
    }

    public function uploadFile()
    {
        $this->validate([
            'uploaded_file'     => 'required'
        ]);

        if(!$this->upload_type){
            $this->dispatch('alert',
                type : 'error',
                message : 'Upload type not set. Please try again.',
            );
            return false;
        }

        $data = CommodityProductOrder::findOrFail($this->hidden_id);

        if($this->upload_type == 'quality_check_image'){
            $data->quality_check_image = [imageUpload($this->uploaded_file, 'quality_check')];
        }

        if($this->upload_type == 'quality_check_certificate'){
            $data->quality_check_certificate = $data->quality_check_certificate ? imageUpload($this->uploaded_file, 'quality_check', $data->quality_check_certificate) : imageUpload($this->uploaded_file, 'quality_check');
        }

        $data->save();

        session()->flash('success', 'File updated successfully !!');
        return $this->redirectRoute('admin.commodity-product-order.show', $this->hidden_id, navigate: true);
    }

    public function generateInvoice($driver_id)
    {
        $data = CommodityProductOrderDriver::find($driver_id);
        if(!$data){
            $this->dispatch('alert',
                type : 'error',
                message : 'Invalide id given. Please try again.',
            );
            return false;
        }
        if(!$data->generate_invoice){
            $this->validate([
                'generate_invoice'     => 'required'
            ]);
            $data->generate_invoice = $this->generate_invoice;
            $data->save();
        }

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

    }

    public function driverDelete($id)
    {
        CommodityProductOrderDriver::destroy($id);
        $this->dispatch('alert',
            type : 'success',
            message : 'Driver remove successfully !!',
        );
    }
}
