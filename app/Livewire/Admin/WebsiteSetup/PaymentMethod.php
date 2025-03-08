<?php

namespace App\Livewire\Admin\WebsiteSetup;

use Livewire\Component;
use App\Models\WebsiteSetup;

class PaymentMethod extends Component
{
    public $page_title = "Payment Method";
    public $value;

    public function mount()
    {
        $this->value = websiteSetupValue('payment_method');
    }

    public function render()
    {
        return view('admin.website_setup.payment_method');
    }

    public function save()
    {
        WebsiteSetup::updateOrCreate(
            ["key" => 'payment_method'],
            [
                "key"   => 'payment_method',
                "value" => $this->value
            ],
        );

        session()->flash('success', 'Payment Method updated successfully !!');
        return $this->redirectRoute('admin.website_setup.payment_methods', navigate: true);
    }
}
