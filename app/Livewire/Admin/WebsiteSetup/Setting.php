<?php

namespace App\Livewire\Admin\WebsiteSetup;

use Livewire\Component;

class Setting extends Component
{
    public $page_title = 'Setting';
    public $enquiry_send_to_seller;
    public function render()
    {
        $this->enquity_send_to_seller = websiteSetupValue('websiteSetupValue');
        return view('admin.website_setup.setting');
    }
}
