<?php

namespace App\Livewire\Admin\Transporter;

use App\Models\User;
use Livewire\Component;
use App\Models\TransporterDetail;

class Create extends Component
{
    public $page_title = 'Create Transporter';
    public $name, $company_name, $gst_number, $phone, $alternate_phone, $aadhar_number, $address;

    public function render()
    {
        return view('admin.transporter.form');
    }

    public function save()
    {
        $this->validate([
            'name'          => 'required',
            'phone'         => 'required|numeric|digits:10',
            'gst_number'    => 'required',
            'address'       => 'required',
            'company_name'  => 'required',
        ]);

        $user = new User;
        $user->name     = $this->name;
        $user->type     = 'transporter';
        $user->phone    = $this->phone;
        $user->save();

        $transporter = new TransporterDetail;
        $transporter->user_id       = $user->id;
        $transporter->company_name  = $this->company_name;
        $transporter->gst_number    = $this->gst_number;
        $transporter->address       = $this->address;
        $transporter->alternate_phone = $this->alternate_phone;
        $transporter->aadhar_number = $this->aadhar_number;
        $transporter->save();

        session()->flash('success', 'Transporter created successfully.');
        return $this->redirectRoute('admin.transporter.index', navigate: true);

    }
}
