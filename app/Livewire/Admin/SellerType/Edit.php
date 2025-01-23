<?php

namespace App\Livewire\Admin\SellerType;

use Livewire\Component;
use App\Models\SellerType;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;
    public $hidden_id, $name, $icon, $thumbnail, $showThumbnail, $banner, $showBanner, $meta_title, $meta_keywords, $meta_description;

    public function render()
    {
        return view('admin.seller_type.form', ['page_title' => 'Edit Seller Type']);
    }

    public function mount($id)
    {
        $this->hidden_id = $id;
        $data = SellerType::find($id);
        $this->name = $data->name;
        $this->icon = $data->icon;
        $this->showThumbnail = $data->thumbnail;
        $this->showBanner = $data->banner;
        $this->meta_title= $data->meta_title;
        $this->meta_keywords = $data->meta_keywords;
        $this->meta_description = $data->meta_description;
    }

    public function update()
    {
        $this->validate([
            'name'  => 'required',
            // 'thumbnail' => 'nullable|image|mimes:jpg,png,jpeg',
            // 'banner'    => 'nullable|image|mimes:jpg,png,jpeg',
            // 'icon' => 'required',
        ]);

        try
        {
            $data = SellerType::find($this->hidden_id);
            $data->name = $this->name;
            $data->slug = Str::slug($this->name);
            $data->icon = $this->icon;
            $data->meta_title = $this->meta_title;
            $data->meta_description = $this->meta_description;
            $data->meta_keywords = $this->meta_keywords;
            if($this->thumbnail){
                $thumbnail_name = time().'-'.rand(10, 99).'.'.$this->thumbnail->extension();
                $data->thumbnail = $this->thumbnail->storeAs('seller', $thumbnail_name, 'public');
            }
            if($this->banner){
                $banner_name = time().'-'.rand(10, 99).'.'.$this->banner->extension();
                $data->banner = $this->banner->storeAs('seller', $banner_name, 'public');
            }
            $data->save();

            session()->flash('success', 'Seller type updated successfully !!');
            return $this->redirect('/admin/seller-type',navigate: true);
        }
        catch (\Exception $e) {
            $this->dispatchBrowserEvent('alert',[
                'type' => 'error',
                'message' => 'something went wrong',
            ]);
        }
    }
}
