<?php

namespace App\Livewire\Admin\BusinessListing;

use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        return view('admin.business_listing.index', ['page_title' => 'Business Listing']);
    }
}
