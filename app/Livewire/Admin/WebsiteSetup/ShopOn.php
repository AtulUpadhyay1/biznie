<?php

namespace App\Livewire\Admin\WebsiteSetup;

use Livewire\Component;
use App\Models\WebsiteSetup;

class ShopOn extends Component
{
    public $page_title = "Shop On Biznie";
    public $title, $description, $video_link;
    public function mount()
    {
        $this->title = websiteSetupValue('shop_on_title');
        $this->description = websiteSetupValue('shop_on_description');
        $this->video_link = websiteSetupValue('shop_on_video_link');
    }

    public function render()
    {
        return view('admin.website_setup.shop_on');
    }

    public function save()
    {
        $this->validate([
            'title'         =>'required',
            'description'    =>'required',
            'video_link'    =>'required|url',
        ]);
        WebsiteSetup::updateOrCreate(
            ["key" => 'shop_on_title'],
            [
                "key"   => 'shop_on_title',
                "value" => $this->title
            ],
        );

        WebsiteSetup::updateOrCreate(
            ["key" => 'shop_on_description'],
            [
                "key"   => 'shop_on_description',
                "value" => $this->description
            ],
        );

        WebsiteSetup::updateOrCreate(
            ["key" => 'shop_on_video_link'],
            [
                "key"   => 'shop_on_video_link',
                "value" => $this->video_link
            ],
        );

        session()->flash('message', 'Shop on Biznie settings saved successfully');
        return $this->redirectRoute('admin.website_setup.shop_on', navigate: true);

    }
}
