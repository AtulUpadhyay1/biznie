<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use App\Models\BusinessCategory;

class BusinessCategoryLivewire extends Component
{
    use WithFileUploads;

    public $formMode = false;

    public $hidden_id, $name, $icon, $thumbnail, $showThumbnail, $banner, $showBanner, $meta_title, $meta_keywords, $meta_description;

    public function render()
    {
        $list = BusinessCategory::latest()->get();
        return view('admin.business_category.index', compact('list'), ['page_title' => 'Business Category']);
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
        $this->icon = null;
        $this->thumbnail = null;
        $this->banner = null;
        $this->showThumbnail = null;
        $this->showBanner = null;
        $this->meta_title= null;
        $this->meta_keywords = null;
        $this->meta_description = null;
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate([
            'name'  => 'required',
            'icon' => 'required',
            'thumbnail' => 'required|image|mimes:jpg,png,jpeg',
            'banner' => 'required|image|mimes:jpg,png,jpeg',
            'meta_title' => 'required',
            'meta_keywords' => 'required',
            'meta_description' => 'required',
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

            $this->formMode = false;
            $this->resetInputFields();

            $this->dispatch('alert',
                type: 'success',
                message: 'Business category created successfully !!'
            );

        }catch (\Exception $e) {

            $this->dispatch('alert',
                type: 'error',
                message: 'Something went wrong !!'
            );
        }
    }

    public function edit($id)
    {
        $this->hidden_id = $id;

        $data = BusinessCategory::find($id);
        $this->name = $data->name;
        $this->icon = $data->icon;
        $this->showThumbnail = $data->thumbnail;
        $this->showBanner = $data->banner;
        $this->meta_title= $data->meta_title;
        $this->meta_keywords = $data->meta_keywords;
        $this->meta_description = $data->meta_description;
        $this->formMode = true;
    }

    public function update()
    {
        $this->validate([
            'name'      => 'required',
            'thumbnail' => 'nullable|image|mimes:jpg,png,jpeg',
            'banner'    => 'nullable|image|mimes:jpg,png,jpeg',
            'icon' => 'required',
            'meta_title' => 'required',
            'meta_keywords' => 'required',
            'meta_description' => 'required',
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
                $thumbnail_name = time().'-'.rand(10, 99).'.'.$this->thumbnail->extension();
                $data->thumbnail = $this->thumbnail->storeAs('business_category', $thumbnail_name, 'public');
            }
            if($this->banner){
                $banner_name = time().'-'.rand(10, 99).'.'.$this->banner->extension();
                $data->banner = $this->banner->storeAs('business_category', $banner_name, 'public');
            }
            $data->save();

            $this->formMode = false;
            $this->resetInputFields();

            $this->dispatch('alert',
                type: 'success',
                message: 'Business category updated successfully !!'
            );

        }catch (\Exception $e) {
            $this->dispatch('alert',
                type: 'error',
                message: 'Something went wrong !!'
            );
        }
    }

    public function updateStatus($id)
    {
        try{

            $data = BusinessCategory::find($id);
            $data->status = $data->status ? 0 : 1;
            $data->save();
            $this->dispatch('alert',
                type: $data->status==1 ? 'success' : 'error',
                message: $data->status== 1 ? 'Business category active successfully !!': 'Business category inactive successfully !!'
            );

        }catch (\Exception $e) {
            $this->dispatch('alert',
                type: 'error',
                message: 'Something went wrong !!'
            );
        }
    }

    public function updateFeatured($id)
    {
        try{
            $data = BusinessCategory::find($id);
            $data->featured = $data->featured ? 0 : 1;
            $data->save();

            $this->dispatch('alert',
                type: $data->featured==1 ? 'success' : 'error',
                message: $data->featured== 1 ? 'Business category featured successfully !!': 'Business category unfeatured successfully !!'
            );

        }catch (\Exception $e) {
            $this->dispatch('alert',
                type: 'error',
                message: 'Something went wrong !!'
            );
        }
    }

    public function delete($id)
    {
        try{

            BusinessCategory::destroy($id);
            $this->dispatch('alert',
                type: 'success',
                message: 'Business category deleted successfully !!'
            );

        }catch (\Exception $e) {
            $this->dispatch('alert',
                type: 'error',
                message: 'Something went wrong !!'
            );
        }

    }
}
