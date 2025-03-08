<?php

namespace App\Livewire\Admin\WebsiteSetup;

use Livewire\Component;
use App\Models\WebsiteSetup;

class Logistics extends Component
{
    public $page_title = "Logistics";
    public $value;

    public function mount()
    {
        $this->value = websiteSetupValue('logistics');
    }

    public function render()
    {
        return view('admin.website_setup.logistics');
    }

    public function save()
    {
        WebsiteSetup::updateOrCreate(
            ["key" => 'logistics'],
            [
                "key"   => 'logistics',
                "value" => $this->value
            ],
        );

        session()->flash('success', 'Logistics settings saved successfully.');
        return $this->redirectRoute('admin.website_setup.logistics', navigate: true);
    }
}
