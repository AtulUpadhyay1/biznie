<?php

namespace App\Livewire\Admin\CommodityProductOrder;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\CommodityProductOrder;

class SendPayment extends Component
{
    use WithFileUploads;
    public $page_title = 'Receive payment';
    public $hidden_id, $data, $mode = 'manual', $transaction_amount, $transaction_account_name, $transaction_account_number, $transaction_bank_name, $transaction_number, $payment_method, $date_time, $description, $file;

    public function mount($id)
    {
        $this->hidden_id = $id;
        $this->data = CommodityProductOrder::with('getBrand', 'getCommodityProduct', 'getDrivers', 'getSeller', 'getCustomer', 'getTransporter', 'getProductEnquiry')->findOrFail($this->hidden_id);
        $this->page_title = 'View Order '. $this->data->getProductEnquiry->unique_id;
    }

    public function render()
    {
        return view('admin.commodity_product_order.send_payment');
    }
}
