<?php

namespace App\Livewire\Admin\Faq;

use App\Models\Faq;
use Livewire\Component;

class Create extends Component
{
    public $page_title = 'Add Faq';

    public $hidden_id, $title, $description;

    public function render()
    {
        return view('admin.faq.form');
    }

    public function save()
    {
        $this->validate([
            'title'        => 'required',
            'description'  => 'required',
        ]);
        $data = new Faq;
        $data->title = $this->title;
        $data->description = $this->description;
        $data->save();
        session()->flash('success', 'FAQ created successfully !!');
        return $this->redirectRoute('admin.faq.index', navigate: true);
    }
}
