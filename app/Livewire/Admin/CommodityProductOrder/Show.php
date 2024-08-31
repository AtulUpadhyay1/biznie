<?php

namespace App\Livewire\Admin\CommodityProductOrder;

use PDF;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\CommodityProductOrder;
use Illuminate\Support\Facades\Storage;
use App\Models\CommodityProductOrderDriver;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Show extends Component
{
    use WithFileUploads;
    public $page_title = 'View Order';
    public $hidden_id, $upload_type, $uploaded_file, $generate_invoice, $eBill_file, $vehicle_notes;

    public function mount($id)
    {
        $this->hidden_id = $id;
    }

    public function render()
    {
        $data = CommodityProductOrder::with('getBrand', 'getCommodityProduct', 'getDrivers', 'getSeller', 'getCustomer', 'getProductEnquiry')->findOrFail($this->hidden_id);
        $this->page_title = 'View Order '. $data->getProductEnquiry->unique_id;
        $this->vehicle_notes = $data->vehicle_notes;
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

    public function qualityCheckImageStatus($status)
    {
        $data = CommodityProductOrder::find($this->hidden_id);
        if (!$data) {
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Invalid id given. Please try again.'
            ]);
            return false;
        }
        $data->quality_check_image_status = $status;
        $data->quality_check_image_status_updated_by = 'admin';
        $data->save();
        session()->flash('success', 'Quality check image status updated successfully !!');
        return $this->redirectRoute('admin.commodity-product-order.show', $this->hidden_id, navigate: true);
    }

    public function generateInvoice($driver_id)
    {
        $data = CommodityProductOrderDriver::find($driver_id);
        if (!$data) {
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Invalid id given. Please try again.'
            ]);
            return false;
        }
        if (!$data->generate_invoice) {
            $this->validate([
                'generate_invoice' => 'required'
            ]);
            $data->generate_invoice = $this->generate_invoice;
            $data->save();
        }
        $driver_detail = $data;
        $order_detail = CommodityProductOrder::with('getBrand', 'getCommodityProduct', 'getDrivers', 'getSeller', 'getCustomer')->findOrFail($this->hidden_id);

        $driver_count = CommodityProductOrderDriver::where('order_id', $this->hidden_id)->whereNotNull('generate_invoice')->count();
        if($driver_count == 1){
            $order_detail->status = "bills generated";
            $history = $order_detail->history;
            $history[] = ['status' => 'Order ' .ucwords($order_detail->status). ' By Admin', 'created_at' => Carbon::now()];
            $order_detail->history = $history;
            $order_detail->save();
        }
        // Create the initial data array
        $data = [
            'order_detail' => $order_detail,
            'driver_detail' => $driver_detail,
        ];

        // Generate PDF content without QR code
        $pdfContent = PDF::loadView('admin.commodity_product_order.print_invoice', $data)->output();

        // Save PDF to storage
        $filePath = 'public/order_invoice/' . $order_detail->order_id . '_invoice.pdf';
        Storage::put($filePath, $pdfContent);

        // Get public URL
        $publicUrl = Storage::url($filePath);

        // Generate QR Code for the public URL
        $qrCode = QrCode::size(100)->generate($publicUrl);
        $data['qrCode'] = $qrCode;

        // Generate PDF content again including the QR code
        $pdfContentWithQrCode = PDF::loadView('admin.commodity_product_order.print_invoice', $data)->output();

        // Save PDF to storage again
        Storage::put($filePath, $pdfContentWithQrCode);

        // Redirect to the public URL
        return redirect($publicUrl);

    }

    public function updateeBill($driver_id)
    {
        $this->validate([
            'eBill_file'     => 'required',
        ]);
        $data = CommodityProductOrderDriver::find($driver_id);
        $data->ebill = $data->ebill ? imageUpload($this->eBill_file, 'ebill', $data->ebill) : imageUpload($this->eBill_file, 'ebill');
        $data->save();

        session()->flash('success', 'eBill File updated successfully !!');
        return $this->redirectRoute('admin.commodity-product-order.show', $this->hidden_id, navigate: true);
    }

    public function driverDelete($id)
    {
        CommodityProductOrderDriver::destroy($id);
        $this->dispatch('alert',
            type : 'success',
            message : 'Driver remove successfully !!',
        );
    }

    public function customerQualityCheck()
    {
        $data = CommodityProductOrder::find($this->hidden_id);
        if (!$data) {
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Invalid id given. Please try again.'
            ]);
            return false;
        }

        $data->customer_quality_check_visibility = $data->customer_quality_check_visibility ? 0 : 1;
        $data->save();
        $this->dispatch('alert',
            type : 'success',
            message : 'Customer quality check status updated successfully !!',
        );
    }

    public function uploadVehicleNotes()
    {
        $data = CommodityProductOrder::find($this->hidden_id);
        if (!$data) {
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Invalid id given. Please try again.'
            ]);
            return false;
        }
        $data->vehicle_notes = $this->vehicle_notes;
        $data->save();

        session()->flash('success', 'Vehicle notes updated successfully !!');
        return $this->redirectRoute('admin.commodity-product-order.show', $this->hidden_id, navigate: true);
    }
}
