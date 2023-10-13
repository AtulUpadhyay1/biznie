<?php

namespace App\Livewire\Admin\Customer;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $list = User::where('type', 'customer')->latest()->paginate(getPaginate());
        return view('admin.customer_list.index', compact('list'), ['page_title' => ' Customer List']);
    }
}
