<?php

namespace App\Livewire\Admin\WebsiteSetup;

use Livewire\Component;
use App\Models\WebsiteSetup;

class ProductAddProcess extends Component
{
    public $page_title = "Product Add Process";
    public $value;

    public function mount()
    {
        $this->value = websiteSetupValue('product_add_process');
    }

    public function render()
    {
        return view('admin.website_setup.product_add_process');
    }

    public function save()
    {
        WebsiteSetup::updateOrCreate(
            ["key" => 'product_add_process'],
            [
                "key"   => 'product_add_process',
                "value" => $this->value
            ],
        );

        session()->flash('success', 'Product add process updated successfully !!');
        return $this->redirectRoute('admin.website_setup.product_add_process', navigate: true);
    }
}
