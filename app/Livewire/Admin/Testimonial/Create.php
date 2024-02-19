<?php

namespace App\Livewire\Admin\Testimonial;

use Livewire\Component;
use App\Models\Testimonial;
use Livewire\WithFileUploads;

class Create extends Component
{
    public $page_title = 'Testimonial Create';

    use WithFileUploads;

    public $hidden_id, $name, $designation, $message, $image, $showImage, $characterCount = 0;

    public function render()
    {
        $this->updatedText();
        return view('admin.testimonial.form');
    }

    public function updatedText()
    {
        // Remove non-alphabet characters
        // $text = preg_replace('/[^A-Za-z]/', '', $this->message);

        // Limit text to 300 characters
        $this->message = substr($this->message, 0, 300);

        // Update character count
        $this->characterCount = strlen($this->message);
    }

    public function save()
    {
        $this->validate([
            'name'          => 'required',
            'designation'   => 'required',
            'message'       => 'required',
            'image'         => 'required|image|mimes:png,jpg,jpeg',
        ]);

        $data = new Testimonial;
        $data->name         = $this->name;
        $data->designation  = $this->designation;
        $data->message      = $this->message;
        $data->image        = imageUpload($this->image, 'testimonial');
        $data->save();
        session()->flash('success', 'Testimonial created successfully !!');
        return $this->redirectRoute('admin.testimonial.index', navigate: true);
    }
}
