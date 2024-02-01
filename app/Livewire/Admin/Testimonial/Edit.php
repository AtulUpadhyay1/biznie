<?php

namespace App\Livewire\Admin\Testimonial;

use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
{
    public $page_title = 'Testimonial Edit';

    use WithFileUploads;

    public $hidden_id, $image, $showImage;

    public function render()
    {
        return view('admin.testimonial.form');
    }
}
