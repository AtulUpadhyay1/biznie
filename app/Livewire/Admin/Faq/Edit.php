<?php

namespace App\Livewire\Admin\Faq;

use App\Models\Faq;
use Livewire\Component;

class Edit extends Component
{
    public $page_title = "Edit FAQ";
    public $hidden_id, $title, $description;

    public function mount($id)
    {
        $this->hidden_id = $id;
        $data = Faq::findOrFail($this->hidden_id);
        $this->title = $data->title;
        $this->description = $data->description;
    }

    public function render()
    {
        return view('admin.faq.form');
    }

    public function update()
    {
        $this->validate([
            'title'        => 'required',
            'description'  => 'required',
        ]);
        $data = Faq::findOrFail($this->hidden_id);
        $data->title = $this->title;
        $data->description = $this->description;
        $data->save();
        session()->flash('success', 'FAQ updated successfully !!');
        return $this->redirectRoute('admin.faq.index', navigate: true);
    }
}
