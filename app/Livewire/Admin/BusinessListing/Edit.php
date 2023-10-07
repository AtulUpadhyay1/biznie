<?php

namespace App\Livewire\Admin\BusinessListing;

use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;

    public function render()
    {
        return view('admin.business_listing.form', ['page_title' => 'Edit Business']);
    }
}
