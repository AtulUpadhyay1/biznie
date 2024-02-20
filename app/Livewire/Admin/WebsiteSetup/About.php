<?php

namespace App\Livewire\Admin\WebsiteSetup;

use Livewire\Component;
use App\Models\WebsiteSetup;

class About extends Component
{
    public $page_title = "About Us";
    public $value;

    public function mount()
    {
        $this->value = websiteSetupValue('about_us');
    }

    public function render()
    {
        return view('admin.website_setup.about');
    }

    public function save()
    {
        WebsiteSetup::updateOrCreate(
            ["key" => 'about_us'],
            [
                "key"   => 'about_us',
                "value" => $this->value
            ],
        );

        session()->flash('success', 'About us updated successfully !!');
        return $this->redirectRoute('admin.website_setup.about', navigate: true);
    }
}
