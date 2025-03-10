<?php

namespace App\Livewire\Admin\WebsiteSetup;

use Livewire\Component;
use App\Models\WebsiteSetup;

class ProductDeliveryInfo extends Component
{
    public $page_title = "Product Delivery Info";
    public $value;

    public function mount()
    {
        $this->value = websiteSetupValue('product_delivery_info');
    }

    public function render()
    {
        return view('admin.website_setup.product_delivery_info');
    }

    public function save()
    {
        WebsiteSetup::updateOrCreate(
            ["key" => 'product_delivery_info'],
            [
                "key"   => 'product_delivery_info',
                "value" => $this->value
            ],
        );

        session()->flash('success', 'Product delivery info updated successfully !!');
        return $this->redirectRoute('admin.website_setup.product_delivery_info', navigate: true);
    }
}
