<?php

namespace App\Livewire\Admin\Brand;

use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;

    public function render()
    {
        return view('admin.brand.form', ['page_title' => 'Edit Brand']);
    }
}
