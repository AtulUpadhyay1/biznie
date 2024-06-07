<?php

namespace App\Livewire\Admin\SellerTag;

use Livewire\Component;
use App\Models\SellerTag;

class Edit extends Component
{
    public $hidden_id, $name;

    public function mount($id)
    {
        $this->hidden_id = $id;
        $data = SellerTag::find($this->hidden_id);
        $this->name = $data->name;
    }

    public function render()
    {
        return view('admin.seller_tag.form', ['page_title' => 'Edit Seller Tag']);
    }

    public function update()
    {
        $this->validate([
            'name'  => 'required|unique:seller_tags,name,'.$this->hidden_id,
        ],[
            'name.required' => 'Enter tag name.',
        ]);

        try {

            $data = SellerTag::find($this->hidden_id);
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
