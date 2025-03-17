<?php

namespace App\Livewire\Admin\ContactUs;

use Livewire\Component;
use App\Models\ContactUs;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $page_title = 'Query List';

    public function render()
    {
        $list = ContactUs::latest()->paginate(getPaginate());
        return view('admin.contact_us.index', compact('list'));
    }
}
