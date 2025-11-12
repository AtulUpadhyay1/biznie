<?php

namespace App\Livewire\Admin\CommodityProductOrder;

use PDF;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\ProductEnquiry;
use App\Models\CommodityProductOrder;
use Illuminate\Support\Facades\Storage;
use App\Models\CommodityProductOrderDriver;
use App\Models\CommodityProductOrderLedger;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\CommodityProductSellerOrderLedger;

class Show extends Component
{
    use WithFileUploads;
    public $page_title = 'View Order';
    public $hidden_id, $upload_type, $uploaded_file, $generate_invoice, $eBill_file, $vehicle_notes;
    public $invoice_name, $invoice_file, $invoice_amount, $ebill, $ebill_expiry_date, $transport_receipt, $debit_note, $debit_note_amount, $credit_note, $credit_note_amount;
    public $seller_invoice_name, $seller_invoice_file, $seller_invoice_amount, $seller_ebill, $seller_ebill_expiry_date, $seller_transport_receipt, $seller_debit_note, $seller_debit_note_amount, $seller_credit_note, $seller_credit_note_amount;

    public function mount($id)
    {
        $this->hidden_id = $id;
    }

    public function render()
    {
        $data = CommodityProductOrder::with('getBrand', 'getCommodityProduct', 'getDrivers', 'getSeller', 'getCustomer', 'getTransporter', 'getProductEnquiry')->findOrFail($this->hidden_id);
        $this->page_title = 'View Order '. $data->getProductEnquiry->unique_id;
        $this->vehicle_notes = $data->vehicle_notes;
        $enquiry_data = ProductEnquiry::with('getBrand', 'getUser', 'getCommodityProduct', 'getCommodityProduct.getCategory', 'getMarkedSellerProductEnquiry', 'getMarkedSellerProductEnquiry.getUser')->findOrFail($data->product_enquiries_id);
        $seller_enquiry_data = $enquiry_data->getMarkedSellerProductEnquiry;
        return view('admin.commodity_product_order.show', compact('data', 'enquiry_data', 'seller_enquiry_data'));
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

        $data = [
            'order_detail' => $order_detail,
            'driver_detail' => $driver_detail,
        ];

        $pdfContent = PDF::loadView('admin.commodity_product_order.print_invoice', $data)->output();

        return response()->streamDownload(function () use ($pdfContent) {
            echo $pdfContent;
        }, $order_detail->order_id . '_invoice.pdf', [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $order_detail->order_id . '_invoice.pdf"'
        ]);

        // // Stream the PDF to the browser for inline viewing
        // return response()->stream(function () use ($pdfContent) {
        //     echo $pdfContent;
        // }, 200, [
        //     'Content-Type' => 'application/pdf',
        //     'Content-Disposition' => 'inline; filename="' . $order_detail->order_id . '_invoice.pdf"'
        // ]);
    }

    // public function generateInvoice($driver_id)
    // {
    //     $data = CommodityProductOrderDriver::find($driver_id);
    //     if (!$data) {
    //         $this->dispatch('alert', [
    //             'type' => 'error',
    //             'message' => 'Invalid id given. Please try again.'
    //         ]);
    //         return false;
    //     }
    //     if (!$data->generate_invoice) {
    //         $this->validate([
    //             'generate_invoice' => 'required'
    //         ]);
    //         $data->generate_invoice = $this->generate_invoice;
    //         $data->save();
    //     }
    //     $driver_detail = $data;
    //     $order_detail = CommodityProductOrder::with('getBrand', 'getCommodityProduct', 'getDrivers', 'getSeller', 'getCustomer')->findOrFail($this->hidden_id);

    //     $driver_count = CommodityProductOrderDriver::where('order_id', $this->hidden_id)->whereNotNull('generate_invoice')->count();
    //     if($driver_count == 1){
    //         $order_detail->status = "bills generated";
    //         $history = $order_detail->history;
    //         $history[] = ['status' => 'Order ' .ucwords($order_detail->status). ' By Admin', 'created_at' => Carbon::now()];
    //         $order_detail->history = $history;
    //         $order_detail->save();
    //     }
    //     // Create the initial data array
    //     $data = [
    //         'order_detail' => $order_detail,
    //         'driver_detail' => $driver_detail,
    //     ];

    //     // Generate PDF content without QR code
    //     $pdfContent = PDF::loadView('admin.commodity_product_order.print_invoice', $data)->output();

    //     // Save PDF to storage
    //     $filePath = 'public/order_invoice/' . $order_detail->order_id . '_invoice.pdf';
    //     Storage::put($filePath, $pdfContent);

    //     // Get public URL
    //     $publicUrl = Storage::url($filePath);

    //     // Generate QR Code for the public URL
    //     $qrCode = QrCode::size(100)->generate($publicUrl);
    //     $data['qrCode'] = $qrCode;

    //     // Generate PDF content again including the QR code
    //     $pdfContentWithQrCode = PDF::loadView('admin.commodity_product_order.print_invoice', $data)->output();

    //     // Save PDF to storage again
    //     Storage::put($filePath, $pdfContentWithQrCode);

    //     // Redirect to the public URL
    //     return redirect($publicUrl);

    // }

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

    public function uploadInvoice()
    {
        $this->validate([
            'invoice_name'  => 'required',
            'invoice_file'  => 'required',
            'invoice_amount'=> 'required|min:1'
        ]);
        $data = CommodityProductOrder::find($this->hidden_id);
        if (!$data) {
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Invalid id given. Please try again.'
            ]);
            return false;
        }
        $invoice_arr = $data->all_invoices ?? [];
        $invoice_data = [
            'uuid'              => \Str::uuid()->toString(),
            'name'              => $this->invoice_name,
            'invoice_file'      => imageUpload($this->invoice_file, 'invoice_file'),
            'ebill'             => $this->ebill ? imageUpload($this->ebill, 'ebill') : NULL,
            'ebill_expiry_date' => $this->ebill_expiry_date,
            'transport_receipt' => $this->transport_receipt ? imageUpload($this->transport_receipt, 'transport_receipt') : NULL,
            'amount'            => $this->invoice_amount ?? 0,
            'debit_note'        => $this->debit_note ? imageUpload($this->debit_note, 'debit_note') : NULL,
            'debit_note_amount' => $this->debit_note_amount ?? 0,
            'credit_note'       => $this->credit_note ? imageUpload($this->credit_note, 'credit_note') : NULL,
            'credit_note_amount'=> $this->credit_note_amount ?? 0,
            'created_at'        => Carbon::now()
        ];

        $invoice_arr[] = $invoice_data;
        $data->all_invoices = $invoice_arr;
        $data->buyer_invoice_amount += $this->invoice_amount ?? 0;
        $data->save();

        $credit_ledger                       = new CommodityProductOrderLedger;
        $credit_ledger->order_id             = $data->id;
        $credit_ledger->transaction_id       = "TNX-".time()."-".rand(1111, 9999);
        $credit_ledger->type                 = 'credit';
        $credit_ledger->amount               = $this->invoice_amount;
        $credit_ledger->remaining_balance    = 0;
        $credit_ledger->description          = 'Amount credited for Order Id: '.$data->order_id;
        $credit_ledger->save();

        session()->flash('success', 'Invoice updated successfully !!');
        return $this->redirectRoute('admin.commodity-product-order.show', $this->hidden_id, navigate: true);
    }

    public function sellerUploadInvoice()
    {
        $this->validate([
            'seller_invoice_name'  => 'required',
            'seller_invoice_file'  => 'required',
            'seller_invoice_amount'=> 'required|min:1'
        ]);
        $data = CommodityProductOrder::find($this->hidden_id);
        if (!$data) {
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Invalid id given. Please try again.'
            ]);
            return false;
        }
        $invoice_arr = $data->seller_invoices ?? [];
        $invoice_data = [
            'uuid'              => \Str::uuid()->toString(),
            'name'              => $this->seller_invoice_name,
            'invoice_file'      => imageUpload($this->seller_invoice_file, 'seller_invoice_file'),
            'ebill'             => $this->seller_ebill ? imageUpload($this->seller_ebill, 'seller_ebill') : NULL,
            'ebill_expiry_date' => $this->seller_ebill_expiry_date,
            'transport_receipt' => $this->seller_transport_receipt ? imageUpload($this->seller_transport_receipt, 'seller_transport_receipt') : NULL,
            'amount'            => $this->seller_invoice_amount ?? 0,
            'debit_note'        => $this->seller_debit_note ? imageUpload($this->seller_debit_note, 'seller_debit_note') : NULL,
            'debit_note_amount' => $this->seller_debit_note_amount ?? 0,
            'credit_note'       => $this->seller_credit_note ? imageUpload($this->seller_credit_note, 'seller_credit_note') : NULL,
            'credit_note_amount'=> $this->seller_credit_note_amount ?? 0,
            'created_at'        => Carbon::now()
        ];

        $invoice_arr[] = $invoice_data;
        $data->seller_invoices = $invoice_arr;
        $data->seller_invoice_amount += $this->seller_invoice_amount ?? 0;
        $data->save();

        $seller_credit_ledger                       = new CommodityProductSellerOrderLedger;
        $seller_credit_ledger->order_id             = $data->id;
        $seller_credit_ledger->transaction_id       = "TNX-".time()."-".rand(1111, 9999);
        $seller_credit_ledger->type                 = 'credit';
        $seller_credit_ledger->amount               = $this->seller_invoice_amount ?? 0;
        $seller_credit_ledger->remaining_balance    = 0;
        $seller_credit_ledger->description          = 'Amount credited for Order Id: '.$data->order_id;
        $seller_credit_ledger->save();

        session()->flash('success', 'Seller invoice updated successfully !!');
        return $this->redirectRoute('admin.commodity-product-order.show', $this->hidden_id, navigate: true);
    }
}
