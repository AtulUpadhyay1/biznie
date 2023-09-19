<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\VendorType;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;

class VendorTypeLivewire extends Component
{
    use WithFileUploads;
    public $formMode = false;

    public $hidden_id, $name, $image, $showIcon;

    public function render()
    {
        $list = VendorType::latest()->get();
        return view('admin.vendor_type.index', compact('list'));
    }

    public function create()
    {
        $this->formMode = true;
        $this->resetInputFields();
    }

    public function cancel()
    {
        $this->formMode = false;
        $this->resetInputFields();
    }

    public function resetInputFields()
    {
        $this->hidden_id = null;
        $this->name = null;
        $this->image = null;
        $this->showIcon = null;
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate([
            'name' => 'required',
            'image' => 'required|image|mimes:jpg,png,jpeg',
        ]);

        try
        {
            $data = new VendorType;
            $data->name = $this->name;
            $data->slug = Str::slug($this->name);
            if($this->image){
                $image_name = time().'-'.rand(10, 99).'.'.$this->image->extension();
                $data->icon = $this->image->storeAs('business_category', $image_name, 'public');
            }
            $data->save();

            $this->formMode = false;
            $this->resetInputFields();
        }
        catch (\Exception $e) {
            $this->dispatchBrowserEvent('alert',[
                'type' => 'error',
                'message' => 'something went wrong',
            ]);
        }
    }

    public function edit($id)
    {
        $this->hidden_id = $id;
        $data = VendorType::find($id);
        $this->name = $data->name;
        $this->showIcon = $data->icon;
        $this->formMode = true;
    }

    public function update()
    {
        $this->validate([
            'name'  => 'required',
            'image' => 'nullable|image|mimes:jpg,png,jpeg',
        ]);

        try
        {
            $data = VendorType::find($this->hidden_id);
            $data->name = $this->name;
            $data->slug = Str::slug($this->name);
            if($this->image){
                $image_name = time().'-'.rand(10, 99).'.'.$this->image->extension();
                $data->icon = $this->image->storeAs('business_category', $image_name, 'public');
            }
            $data->save();

            $this->formMode = false;
            $this->resetInputFields();
        }
        catch (\Exception $e) {
            $this->dispatchBrowserEvent('alert',[
                'type' => 'error',
                'message' => 'something went wrong',
            ]);
        }
    }

    public function updateStatus($id)
    {
        $data = VendorType::find($id);
        $data->status = $data->status ? 0 : 1;
        $data->save();
    }

    public function updateFeatured($id)
    {
        $data = VendorType::find($id);
        $data->featured = $data->featured ? 0 : 1;
        $data->save();
    }

    public function delete($id)
    {
        VendorType::destroy($id);
    }
}
