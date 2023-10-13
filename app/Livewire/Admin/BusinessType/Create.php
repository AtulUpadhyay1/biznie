<?php

namespace App\Livewire\Admin\BusinessType;

use Livewire\Component;
use App\Models\BusinessType;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;

class Create extends Component
{

    use WithFileUploads;
    public $hidden_id, $name, $icon, $thumbnail, $showThumbnail, $banner, $showBanner, $meta_title, $meta_keywords, $meta_description;

    public function render()
    {
        return view('admin.business_type.form', ['page_title' => 'Create Business Type']);
    }

    public function save()
    {
        $this->validate([
            'name' => 'required',
            'thumbnail' => 'required|image|mimes:jpg,png,jpeg',
            'banner' => 'required|image|mimes:jpg,png,jpeg',
            'icon' => 'required',
        ]);

        try
        {
            $data = new BusinessType;
            $data->name = $this->name;
            $data->slug = Str::slug($this->name);
            $data->icon = $this->icon;
            $data->meta_title = $this->meta_title;
            $data->meta_description = $this->meta_description;
            $data->meta_keywords = $this->meta_keywords;
            if($this->thumbnail){
                $data->thumbnail = imageUpload($this->thumbnail, 'business_type');
            }
            if($this->banner){
                $data->banner = imageUpload($this->banner, 'business_type');
            }
            $data->save();

            session()->flash('success', 'Business type created successfully !!');
            return $this->redirect('/admin/business-type',navigate: true);
        }
        catch (\Exception $e) {
            $this->dispatchBrowserEvent('alert',[
                'type' => 'error',
                'message' => 'something went wrong',
            ]);
        }
    }

}
