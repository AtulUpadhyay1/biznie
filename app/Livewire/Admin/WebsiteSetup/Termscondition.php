<?php

namespace App\Livewire\Admin\WebsiteSetup;

use Livewire\Component;
use App\Models\WebsiteSetup;

class Termscondition extends Component
{

    public $page_title = "Terms and conditions";
    public $value;

    public function mount()
    {
        $this->value = websiteSetupValue('terms_condition');
    }

    public function render()
    {
        return view('admin.website_setup.termscondition');
    }

    public function save()
    {
        WebsiteSetup::updateOrCreate(
            ["key" => 'terms_condition'],
            [
                "key"   => 'terms_condition',
                "value" => $this->value
            ],
        );

        session()->flash('success', 'T&C updated successfully !!');
        return $this->redirectRoute('admin.website_setup.terms', navigate: true);
    }
}
