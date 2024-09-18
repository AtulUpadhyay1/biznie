<?php

namespace App\Livewire\Admin\Transporter;

use App\Models\User;
use Livewire\Component;
use App\Models\TransporterDetail;

class Edit extends Component
{
    public $page_title = 'Update Transporter';
    public $hidden_id, $name, $company_name, $gst_number, $phone, $alternate_phone, $aadhar_number, $address;

    public function mount($id)
    {
        $this->hidden_id      = $id;
        $user = User::findOrFail($this->hidden_id);
        $this->name     = $user->name;
        $this->phone    = $user->phone;

        $transporter = TransporterDetail::where('user_id', $user->id)->firstOrFail();
        $this->company_name  = $transporter->company_name;
        $this->gst_number    = $transporter->gst_number;
        $this->address       = $transporter->address;
        $this->alternate_phone = $transporter->alternate_phone;
        $this->aadhar_number = $transporter->aadhar_number;
        $this->address       = $transporter->address;

    }

    public function render()
    {
        return view('admin.transporter.form');
    }

    public function update()
    {
        $this->validate([
            'name'          => 'required',
            'phone'         => 'required|numeric|digits:10',
            'gst_number'    => 'required',
            'address'       => 'required',
            'company_name'  => 'required',
        ]);

        $user = User::findOrFail($this->hidden_id);
        $user->name     = $this->name;
        $user->type     = 'transporter';
        $user->phone    = $this->phone;
        $user->save();

        $transporter = TransporterDetail::where('user_id', $user->id)->first();
        $transporter->company_name  = $this->company_name;
        $transporter->gst_number    = $this->gst_number;
        $transporter->address       = $this->address;
        $transporter->alternate_phone = $this->alternate_phone;
        $transporter->aadhar_number = $this->aadhar_number;
        $transporter->address       = $this->address;
        $transporter->save();

        session()->flash('success', 'Transporter updated successfully.');
        return $this->redirectRoute('admin.transporter.index', navigate: true);

    }
}
