<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use App\Models\ProductCategory;
use App\Models\BusinessCategory;
use App\Models\ProductSubCategory;

class ProductSubCategoryLiveWire extends Component
{
    use WithFileUploads;
    public $formMode = false;
    public $hidden_id, $product_category_id, $business_category_id, $name, $image, $showIcon;
    public $product_category_list = [];
    public $business_category_list  = null;

    public function render()
    {
        $list = ProductSubCategory::latest()->get();
        return view('admin.product_sub_category.index', compact('list'));
    }

    public function setProductCategoryList()
    {
        $this->product_category_list=ProductCategory::where('business_category_id',$this->business_category_id)->get();
    }

    public function create()
    {
        $this->formMode = true;
        $this->business_category_list=BusinessCategory::where('status',1)->get();
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
        $this->product_category_id = null;
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

        $data = new ProductSubCategory;
        $data->product_categories_id = $this->product_category_id;
        $data->business_category_id = $this->business_category_id;
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

    public function edit($id)
    {
        $this->hidden_id = $id;
        $data = ProductSubCategory::find($id);
        $this->business_category_list=BusinessCategory::where('status',1)->get();
        $this->product_category_list=ProductCategory::where('business_category_id',$data->business_category_id)->get();
        $this->name = $data->name;
        $this->product_category_id = $data->product_categories_id;
        $this->business_category_id = $data->business_category_id;
        $this->showIcon = $data->icon;
        $this->formMode = true;
    }

    public function update()
    {
        $this->validate([
            'name'  => 'required',
            'image' => 'nullable|image|mimes:jpg,png,jpeg',
        ]);


        $data = ProductSubCategory::find($this->hidden_id);
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

    public function updateStatus($id)
    {
        $data = ProductSubCategory::find($id);
        $data->status = $data->status ? 0 : 1;
        $data->save();
    }

    public function updateFeatured($id)
    {
        $data = ProductSubCategory::find($id);
        $data->featured = $data->featured ? 0 : 1;
        $data->save();
    }

    public function delete($id)
    {
        ProductSubCategory::destroy($id);
    }
}
