<?php

namespace App\Livewire\Admin\Customer;

use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;

class EditInfo extends Component
{
    use WithFileUploads;

    public $name, $email, $phone;

    public $data;
    public function mount($id)
    {
        $this->data = User::find($id);
    }

    public function render()
    {
        $this->edit();
        return view('admin.customer_list.edit', ['page_title' => 'Edit Customer Info']);
    }

    public function edit()
    {
        $this->name = $this->data->name;
    }
}
