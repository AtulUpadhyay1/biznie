<?php

namespace App\Livewire\Admin\Brand;

use Livewire\Component;
use Livewire\WithFileUploads;

class Index extends Component
{
    use WithFileUploads;

    public function render()
    {
        return view('admin.brand.index', ['page_title' => 'Brand']);
    }
}
