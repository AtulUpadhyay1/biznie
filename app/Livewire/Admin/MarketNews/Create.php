<?php

namespace App\Livewire\Admin\MarketNews;

use Livewire\Component;
use App\Models\MarketNews;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;

class Create extends Component
{
    public $page_title = 'Market News Create';

    use WithFileUploads;

    public $hidden_id, $title, $description, $tags, $image, $showImage;

    public function render()
    {
        return view('admin.market_news.form');
    }

    public function save()
    {
        $this->validate([
            'title'         => 'required',
            'description'   => 'required',
            //'tags'          => 'required',
            'image'         => 'required|image|mimes:png,jpg,jpeg',
        ]);

        $data = new MarketNews;
        $data->title        = $this->title;
        $data->slug         = Str::slug($this->title);
        $data->description  = $this->description;
        $data->tags         = $this->tags;
        $data->image        = imageUpload($this->image, 'market_news');
        $data->added_by     = auth()->id();
        $data->save();
        session()->flash('success', 'Product created successfully !!');
        return $this->redirectRoute('admin.market-news.index', navigate: true);

    }
}
