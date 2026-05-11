<?php

namespace App\Livewire\Admin\Customer;

use App\Models\User;
use Livewire\Component;
use App\Models\UserDetail;

class Create extends Component
{
    public $page_title = 'Create Customer';
    public $hidden_id, $name, $company_name, $gst_number, $type="customer", $phone;

    public function render()
    {
        return view('admin.customer_list.create');
    }

    public function save()
    {
        $this->validate([
            'name'          => 'required',
            'company_name'  => 'required',
            'gst_number'    => 'required',
            'phone'         => 'required|numeric|digits:10',
        ]);

        $data = new User;
        $data->name         = $this->name;
        $data->type         = $this->type;
        $data->phone        = $this->phone;
        $data->save();

        $detail = new UserDetail;
        $detail->user_id      = $data->id;
        $detail->company_name = $this->company_name;
        $detail->gst_number    = $this->gst_number;
        $detail->save();

        session()->flash('success', 'Customer created successfully !!');
        return $this->redirectRoute('admin.customer-list', navigate: true);
    }
}
