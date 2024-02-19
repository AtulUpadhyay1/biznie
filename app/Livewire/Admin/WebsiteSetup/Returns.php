<?php

namespace App\Livewire\Admin\WebsiteSetup;

use Livewire\Component;
use App\Models\WebsiteSetup;

class Returns extends Component
{
    public $page_title = "Returns Policy";
    public $value;

    public function render()
    {
        $this->value = websiteSetupValue('returns_policy');
        return view('admin.website_setup.returns');
    }

    public function save()
    {
        WebsiteSetup::updateOrCreate(
            ["key" => 'returns_policy'],
            [
                "key"   => 'returns_policy',
                "value" => $this->value
            ],
        );

        session()->flash('success', 'Returns policy updated successfully !!');
        return $this->redirectRoute('admin.website_setup.returns', navigate: true);
    }
}
