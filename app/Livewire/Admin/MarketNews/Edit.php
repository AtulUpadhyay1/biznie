<?php

namespace App\Livewire\Admin\MarketNews;

use Livewire\Component;
use App\Models\MarketNews;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;

class Edit extends Component
{
    public $page_title = 'Market News Edit';

    use WithFileUploads;

    public $hidden_id, $title, $description, $tags, $image, $showImage;

    public function mount($id)
    {
        $this->hidden_id = $id;
        $data = MarketNews::find($this->hidden_id);
        $this->title        = $data->title;
        $this->description  = $data->description;
        $this->tags         = $data->tags;
        $this->showImage    = imageUrl($data->image);
    }

    public function render()
    {
        return view('admin.market_news.form');
    }

    public function update()
    {
        $this->validate([
            'title'         => 'required',
            'description'   => 'required',
            //'tags'          => 'required',
            'image'         => 'nullable|image|mimes:png,jpg,jpeg',
        ]);

        $data = MarketNews::find($this->hidden_id);
        $data->title        = $this->title;
        $data->slug         = Str::slug($this->title);
        $data->description  = $this->description;
        $data->tags         = $this->tags;
        $data->image        = $this->image ? imageUpload($this->image, 'market_news') : $this->image;
        $data->added_by     = auth()->id();
        $data->save();
        session()->flash('success', 'Product created successfully !!');
        return $this->redirectRoute('admin.market-news.index', navigate: true);

    }
}
