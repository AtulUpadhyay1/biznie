<?php

namespace App\Livewire\Admin\SellerTag;

use Livewire\Component;
use App\Models\SellerTag;

class Create extends Component
{
    public $hidden_id, $name;

    public function render()
    {
        return view('admin.seller_tag.form', ['page_title' => 'Create Seller Tag']);
    }

    public function save()
    {
        $this->validate([
            'name'  => 'required|unique:seller_tags,name',
        ],[
            'name.required' => 'Enter tag name.',
        ]);

        try {

            $data = new SellerTag;
            $data->name = $this->name;
            $data->save();

            session()->flash('success', 'Seller tag created successfully !!');
            return $this->redirectRoute('admin.seller-tag.index',navigate: true);

        } catch (\Throwable $th) {
            $this->dispatch('alert',
                type : 'error',
                message : 'something went wrong',
            );
        }
    }
}
