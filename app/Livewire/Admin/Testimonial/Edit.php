<?php

namespace App\Livewire\Admin\Testimonial;

use Livewire\Component;
use App\Models\Testimonial;
use Livewire\WithFileUploads;

class Edit extends Component
{
    public $page_title = 'Testimonial Edit';

    use WithFileUploads;

    public $hidden_id, $name, $designation, $message, $image, $showImage, $characterCount = 0;

    public function mount($id)
    {
        $this->hidden_id = $id;
        $data = Testimonial::find($this->hidden_id);
        $this->name         = $data->name;
        $this->designation  = $data->designation;
        $this->message      = $data->message;
        $this->showImage    = imageUrl($data->image);
    }

    public function render()
    {
        return view('admin.testimonial.form');
    }

    public function update()
    {
        $this->validate([
            'name'          => 'required',
            'designation'   => 'required',
            'message'       => 'required',
            'image'         => 'nullable|image|mimes:png,jpg,jpeg',
        ]);

        $data = Testimonial::find($this->hidden_id);
        $data->name         = $this->name;
        $data->designation  = $this->designation;
        $data->message      = $this->message;
        $data->image        = $this->image ? imageUpload($this->image, 'testimonial', $data->image) : $data->image;
        $data->save();
        session()->flash('success', 'Testimonial created successfully !!');
        return $this->redirectRoute('admin.testimonial.index', navigate: true);
    }
}
