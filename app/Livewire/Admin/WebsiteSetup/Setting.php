<?php

namespace App\Livewire\Admin\WebsiteSetup;

use Livewire\Component;
use App\Models\WebsiteSetup;

class Setting extends Component
{
    public $page_title = 'Setting';
    public $enquiry_send_to_seller;

    public function render()
    {
        $this->enquiry_send_to_seller = websiteSetupValue('enquiry_send_to_seller');
        return view('admin.website_setup.setting');
    }

    public function updateSetting($key)
    {
        try {
            WebsiteSetup::updateOrCreate(
                ["key"      => 'enquiry_send_to_seller'],
                [
                    "key"   => 'enquiry_send_to_seller',
                    "value" => $this->enquiry_send_to_seller ? 0 : 1,
                ],
            );

            $this->dispatch('alert',
                type: 'success',
                message: 'Setting has been updated successfully.'
            );
        } catch (\Throwable $th) {
            $this->dispatch('alert',
                type: 'error',
                message: 'Something went wrong !!'
            );
        }
    }
}
