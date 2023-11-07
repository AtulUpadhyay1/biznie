<?php

namespace App\Livewire\Admin\Banner;

use App\Models\Banner;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    public $page_title = 'Banner List';

    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $list = Banner::orderBy('created_at','desc')->paginate(getPaginate());
        return view('admin.banner.index', compact('list'));
    }
}
