<?php

namespace App\Livewire\Admin\WebsiteSetup;

use Livewire\Component;
use App\Models\WebsiteSetup;

class General extends Component
{
    public $page_title = "General Setup";
    public $value = [];

    public function render()
    {
        $this->value['phone1'] = websiteSetupValue('phone1');
        $this->value['phone2'] = websiteSetupValue('phone2');
        $this->value['whatsapp'] = websiteSetupValue('whatsapp');
        $this->value['email'] = websiteSetupValue('email');
        $this->value['short_about'] = websiteSetupValue('short_about');
        $this->value['address'] = websiteSetupValue('address');
        $this->value['facebook'] = websiteSetupValue('facebook');
        $this->value['twitter'] = websiteSetupValue('twitter');
        $this->value['instagram'] = websiteSetupValue('instagram');
        $this->value['youtube'] = websiteSetupValue('youtube');

        return view('admin.website_setup.general');
    }

    public function update()
    {
        try {
            foreach ($this->value as $key => $value) {
                WebsiteSetup::updateOrCreate(
                    ["key"      => $key],
                    [
                        "key"   => $key,
                        "value" => $this->value[$key],
                    ],
                );
            }

            $this->dispatch('alert',
                type: 'success',
                message: 'Settings have been updated successfully.'
            );

        } catch (\Throwable $th) {

            $this->dispatch('alert',
                type: 'error',
                message: 'Something went wrong !!'
            );

        }
    }
}
