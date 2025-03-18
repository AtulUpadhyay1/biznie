<?php

namespace App\Livewire\Admin\GeneralEnquiry;

use Livewire\Component;
use App\Models\GeneralEnquiry;

class Show extends Component
{
    public $hidden_id;

    public $page_title = 'General Enquiry';

    public function mount($id)
    {
        $this->hidden_id = $id;
    }

    public function render()
    {
        $data = GeneralEnquiry::with('getBrand', 'getSellerCommodityProduct')->findOrFail($this->hidden_id);
        $this->page_title = 'General Enquiry: '. $data->unique_id;
        return view('admin.general_enquiry.show', compact('data'));
    }
}
