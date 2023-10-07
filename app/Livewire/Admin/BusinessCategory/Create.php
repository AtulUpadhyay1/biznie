<?php

namespace App\Livewire\Admin\BusinessCategory;

use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use App\Models\BusinessCategory;

class Create extends Component
{
    use WithFileUploads;
    public $hidden_id, $name, $icon, $thumbnail, $showThumbnail, $banner, $showBanner,
    $meta_title, $meta_keywords, $meta_description;

    public function render()
    {
        return view('admin.business_category.form', ['page_title' => 'Create Business Category']);
    }

    public function save()
    {
        $this->validate([
            'name'  => 'required',
            'icon' => 'required',
            'thumbnail' => 'required|image|mimes:jpg,png,jpeg',
            'banner' => 'required|image|mimes:jpg,png,jpeg',
        ]);

        try{
            $data = new BusinessCategory;
            $data->name = $this->name;
            $data->slug = Str::slug($this->name);
            $data->icon = $this->icon;
            $data->meta_title = $this->meta_title;
            $data->meta_description = $this->meta_description;
            $data->meta_keywords = $this->meta_keywords;
            if($this->thumbnail){
                $thumbnail_name = time().'-'.rand(10, 99).'.'.$this->thumbnail->extension();
                $data->thumbnail = $this->thumbnail->storeAs('business_category', $thumbnail_name, 'public');
            }
            if($this->banner){
                $banner_name = time().'-'.rand(10, 99).'.'.$this->banner->extension();
                $data->banner = $this->banner->storeAs('business_category', $banner_name, 'public');
            }
            $data->save();

            session()->flash('success', 'Business category created successfully !!');
            return $this->redirect('/admin/business-category',navigate: true);

        }catch (\Exception $e) {

            $this->dispatch('alert',
                type: 'error',
                message: 'Something went wrong !!'
            );
        }
    }
}
