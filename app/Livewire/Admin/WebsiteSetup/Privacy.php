<?php

namespace App\Livewire\Admin\WebsiteSetup;

use Livewire\Component;
use App\Models\WebsiteSetup;

class Privacy extends Component
{
    public $page_title = "Privacy Policy";
    public $value;

    public function render()
    {
        $this->value = websiteSetupValue('privacy_policy');
        return view('admin.website_setup.privacy');
    }

    public function save()
    {
        WebsiteSetup::updateOrCreate(
            ["key" => 'privacy_policy'],
            [
                "key"   => 'privacy_policy',
                "value" => $this->value
            ],
        );

        session()->flash('success', 'Privacy policy updated successfully !!');
        return $this->redirectRoute('admin.website_setup.privacy', navigate: true);
    }
}
