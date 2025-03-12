<?php

namespace App\Livewire\Admin\CommodityProductEnquiry;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\Notification;
use Livewire\WithPagination;
use App\Models\ProductEnquiry;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $page_title = 'Commodity Product Enquiry';

    public function render()
    {
        $total = ProductEnquiry::count();
        $list = ProductEnquiry::latest()->with('getBrand', 'getCommodityProduct', 'getUser', 'getCommodityProductOrder')->paginate(getPaginate());
        $this->markAsRead();
        return view('admin.commodity_product_enquiry.index', compact('total', 'list'));
    }

    public function markAsRead()
    {
        $notifications = Notification::where('title', 'New Product Enquiry')
            ->where('is_admin_read', 0)
            ->get();
        foreach ($notifications as $notification) {
            $notification->is_admin_read = 1;
            $notification->save();
        }
    }

    public function processOverPhone($id)
    {
        $data = ProductEnquiry::find($id);
        if(!$data){
            $this->dispatch('alert',
                type : 'error',
                message : 'Invalid Product Enquiry ID.',
            );
            return false;
        }
        $data->status = 'Process Over Phone';
        $history = $data->history;
        $history[] = ['status' => 'Process Over Phone', 'created_at' => Carbon::now()];
        $data->history = $history;
        $data->save();

        $this->dispatch('alert',
            type :'success',
            message : 'Product Enquiry status updated successfully.',
        );

    }
}
