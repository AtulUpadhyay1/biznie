<?php

namespace App\Livewire\Admin\WebsiteSetup;

use Livewire\Component;
use App\Models\WebsiteSetup;

class ProductTermsCondition extends Component
{
    public $page_title = "Product Terms & Condition";
    public $value;

    public function mount()
    {
        $this->value = websiteSetupValue('product_terms_condition');
    }

    public function render()
    {
        return view('admin.website_setup.product_terms_condition');
    }

    public function save()
    {
        WebsiteSetup::updateOrCreate(
            ["key" => 'product_terms_condition'],
            [
                "key"   => 'product_terms_condition',
                "value" => $this->value
            ],
        );

        session()->flash('success', 'Product terms and conditions updated successfully!');
        return $this->redirectRoute('admin.website_setup.product_terms', navigate: true);
    }
}
