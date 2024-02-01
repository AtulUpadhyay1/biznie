<?php

namespace App\Livewire\Admin\BusinessCategory;

use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use App\Models\BusinessCategory;

class Edit extends Component
{
    use WithFileUploads;
    public $hidden_id, $name, $icon, $thumbnail, $showThumbnail, $banner, $showBanner,
    $meta_title, $meta_keywords, $meta_description;

    public function mount($id)
    {
        $this->hidden_id = $id;
        $data = BusinessCategory::find($id);
        $this->name = $data->name;
        $this->icon = $data->icon;
        $this->showThumbnail = imageUrl($data->thumbnail);
        $this->showBanner = imageUrl($data->banner);
        $this->meta_title= $data->meta_title;
        $this->meta_keywords = $data->meta_keywords;
        $this->meta_description = $data->meta_description;
    }

    public function render()
    {
        return view('admin.business_category.form', ['page_title' => 'Edit Business Category']);
    }

    public function update()
    {
        $this->validate([
            'name'      => 'required',
            'thumbnail' => 'nullable|image|mimes:jpg,png,jpeg',
            'banner'    => 'nullable|image|mimes:jpg,png,jpeg',
            'icon' => 'required',
        ]);

        try{
            $data = BusinessCategory::find($this->hidden_id);
            $data->name = $this->name;
            $data->slug = Str::slug($this->name);
            $data->icon = $this->icon;
            $data->meta_title = $this->meta_title;
            $data->meta_description = $this->meta_description;
            $data->meta_keywords = $this->meta_keywords;
            if($this->thumbnail){
                $data->thumbnail = imageUpload($this->thumbnail, 'business_category');
            }
            if($this->banner){
                $data->banner = imageUpload($this->banner, 'business_category');
            }
            $data->save();

            session()->flash('success', 'Business category updated successfully !!');
            return $this->redirectRoute('admin.business-category',navigate: true);

        }catch (\Exception $e) {
            $this->dispatch('alert',
                type: 'error',
                message: 'Something went wrong !!'
            );
        }
    }
}
