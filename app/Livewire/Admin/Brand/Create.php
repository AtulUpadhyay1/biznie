<?php

namespace App\Livewire\Admin\Brand;

use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public function render()
    {
        return view('admin.brand.form', ['page_title' => 'Create Brand']);
    }
}
