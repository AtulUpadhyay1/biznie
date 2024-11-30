<?php

namespace App\Livewire\Admin\Customer;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ProductEnquiry;

class Enquiry extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $data;
    public function mount($id)
    {
        $this->data = User::find($id);
    }

    public function render()
    {
        $list = ProductEnquiry::where('user_id', $this->data->id)->latest()->simplePaginate(getPaginate(25));
        return view('admin.customer_list.enquiry', compact('list'), ['page_title' => 'Customer Enquiry']);
    }
}
